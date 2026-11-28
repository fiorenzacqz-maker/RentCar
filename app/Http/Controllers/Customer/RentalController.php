<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\PublicStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RentalController extends Controller
{
    /**
     * Tampilkan daftar seluruh booking milik customer.
     */
    public function index(Request $request): View
    {
        Booking::expireOverdue();

        $bookings = $request->user()
            ->bookings()
            ->with(['car.category', 'payment', 'carReturn', 'review'])
            ->latest()
            ->paginate(10);

        return view('customer.rentals.index', compact('bookings'));
    }

    /**
     * Tampilkan form pemesanan mobil.
     */
    public function create(Request $request): View
    {
        Booking::expireOverdue();

        $cars = Car::with('category')
            ->where('status', 'available')
            ->orderBy('brand')
            ->orderBy('name')
            ->get();

        $selectedCar = null;
        if ($request->filled('car_id')) {
            $selectedCar = $cars->firstWhere('id', (int) $request->integer('car_id'));
        }

        // Jadwal booking aktif per mobil untuk ditampilkan ke user
        $bookedDates = Booking::query()
            ->whereIn('booking_status', ['pending', 'confirmed', 'ongoing'])
            ->where('end_date', '>=', now())
            ->get(['car_id', 'start_date', 'end_date'])
            ->groupBy('car_id')
            ->map(fn ($items) => $items->map(fn ($b) => [
                'start' => $b->start_date->format('d M Y H:i'),
                'end' => $b->end_date->format('d M Y H:i'),
            ])->values());

        return view('customer.rentals.create', [
            'cars' => $cars,
            'selectedCar' => $selectedCar,
            'bookedDates' => $bookedDates,
            'driverFeePerDay' => (float) Setting::get('driver_fee_per_day'),
            'deadlineMinutes' => (int) Setting::get('payment_deadline_minutes'),
            'bankInfo' => (string) Setting::get('bank_info'),
        ]);
    }

    /**
     * Simpan pemesanan mobil baru beserta data pembayaran awal.
     * Total dihitung ulang di server; nilai dari klien tidak dipercaya.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'borrower_name' => ['required', 'string', 'max:100'],
            'borrower_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{6,20}$/'],
            'start_date' => ['required', 'date', 'after_or_equal:' . now()->subMinutes(5)->toDateTimeString()],
            'end_date' => ['required', 'date', 'after:start_date'],
            'service_type' => ['required', 'in:self_drive,with_driver'],
            'payment_method' => ['required', 'in:Transfer Bank,E-Wallet,Tunai'],
            'proof_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'car_id.required' => 'Silakan pilih mobil terlebih dahulu.',
            'borrower_name.required' => 'Nama peminjam wajib diisi.',
            'borrower_phone.required' => 'Nomor kontak peminjam wajib diisi.',
            'borrower_phone.regex' => 'Format nomor kontak tidak valid.',
            'start_date.after_or_equal' => 'Waktu pinjam tidak boleh di masa lalu.',
            'end_date.after' => 'Waktu kembali harus setelah waktu pinjam.',
            'service_type.required' => 'Pilih layanan sewa: Lepas Kunci atau Dengan Sopir.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'proof_image.image' => 'Bukti pembayaran harus berupa gambar.',
            'proof_image.max' => 'Ukuran bukti pembayaran maksimal 2MB.',
        ]);

        Booking::expireOverdue();

        $car = Car::findOrFail($validated['car_id']);

        if ($car->status !== 'available') {
            return back()->withInput()->withErrors([
                'car_id' => 'Mobil ini sedang tidak tersedia untuk disewa.',
            ]);
        }

        if (Booking::hasDateConflict($car->id, $validated['start_date'], $validated['end_date'])) {
            return back()->withInput()->withErrors([
                'start_date' => 'Mobil sudah dipesan pada rentang waktu tersebut. Silakan pilih waktu lain.',
            ]);
        }

        $withDriver = $validated['service_type'] === 'with_driver';
        $pricing = Booking::calculatePricing($car, $validated['start_date'], $validated['end_date'], $withDriver);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = PublicStorageService::store($request->file('proof_image'), 'payments');
        }

        $booking = DB::transaction(function () use ($request, $car, $validated, $pricing, $withDriver, $proofPath): Booking {
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'car_id' => $car->id,
                'borrower_name' => $validated['borrower_name'],
                'borrower_phone' => $validated['borrower_phone'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $pricing['days'],
                'with_driver' => $withDriver,
                'driver_fee' => $pricing['driver_fee'],
                'total_price' => $pricing['total'],
                'booking_status' => 'pending',
                'payment_deadline' => now()->addMinutes((int) Setting::get('payment_deadline_minutes')),
                'notes' => $validated['notes'] ?? null,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => $validated['payment_method'],
                'amount' => $pricing['total'],
                'proof_image' => $proofPath,
                'payment_status' => 'pending',
                'paid_at' => $proofPath ? now() : null,
            ]);

            return $booking;
        });

        return redirect()
            ->route('customer.rentals.show', $booking)
            ->with('success', 'Booking berhasil dibuat! Selesaikan pembayaran sebelum ' . $booking->payment_deadline->format('d M Y H:i') . '.');
    }

    /**
     * Tampilkan detail booking milik customer.
     */
    public function show(Request $request, Booking $booking): View
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak berhak melihat booking ini.');
        }

        Booking::expireOverdue();
        $booking->refresh()->load(['car.category', 'payment', 'carReturn', 'review']);

        return view('customer.rentals.show', [
            'booking' => $booking,
            'bankInfo' => (string) Setting::get('bank_info'),
            'qrisUrl' => Setting::qrisUrl(),
        ]);
    }

    /**
     * Batalkan booking yang masih berstatus pending.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($booking->booking_status !== 'pending') {
            return back()->with('error', 'Hanya booking berstatus pending yang dapat dibatalkan.');
        }

        $booking->update(['booking_status' => 'cancelled']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Tampilkan status pengembalian mobil milik customer.
     */
    public function returns(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['car', 'carReturn'])
            ->whereIn('booking_status', ['ongoing', 'completed'])
            ->latest()
            ->get();

        return view('customer.returns.index', compact('bookings'));
    }
}
