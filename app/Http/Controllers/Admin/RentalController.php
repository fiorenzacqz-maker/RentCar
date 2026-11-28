<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\CarReturn;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RentalController extends Controller
{
    /**
     * Tampilkan seluruh daftar booking/rental.
     */
    public function index(Request $request): View
    {
        Booking::expireOverdue();

        $status = $request->query('status');

        $bookings = Booking::with(['user', 'car', 'payment', 'carReturn'])
            ->when($status, fn ($q) => $q->where('booking_status', $status))
            ->latest()
            ->paginate(12);

        return view('admin.rentals.index', compact('bookings', 'status'));
    }

    /**
     * Tampilkan form tambah booking oleh admin.
     */
    public function create(Request $request): View
    {
        $customers = User::where('role', User::ROLE_CUSTOMER)->orderBy('name')->get();
        $cars = Car::where('status', 'available')->orderBy('brand')->orderBy('name')->get();
        $selectedCarId = $request->integer('car_id') ?: null;

        $driverFeePerDay = (float) Setting::get('driver_fee_per_day');

        return view('admin.rentals.create', compact('customers', 'cars', 'selectedCarId', 'driverFeePerDay'));
    }

    /**
     * Simpan booking baru oleh admin/petugas.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_CUSTOMER)],
            'car_id' => ['required', 'exists:cars,id'],
            'borrower_name' => ['required', 'string', 'max:100'],
            'borrower_phone' => ['required', 'string', 'max:20'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'with_driver' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'in:Transfer Bank,E-Wallet,Tunai'],
            'payment_status' => ['required', 'in:pending,verified'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'borrower_name.required' => 'Nama peminjam wajib diisi.',
            'borrower_phone.required' => 'Nomor kontak peminjam wajib diisi.',
            'end_date.after' => 'Waktu kembali harus setelah waktu pinjam.',
        ]);

        $car = Car::findOrFail($validated['car_id']);

        if ($car->status !== 'available') {
            return back()->withInput()->withErrors(['car_id' => 'Mobil sedang tidak tersedia.']);
        }

        if (Booking::hasDateConflict($car->id, $validated['start_date'], $validated['end_date'])) {
            return back()->withInput()->withErrors(['start_date' => 'Jadwal mobil bertabrakan dengan booking lain.']);
        }

        $withDriver = $request->boolean('with_driver');
        $pricing = Booking::calculatePricing($car, $validated['start_date'], $validated['end_date'], $withDriver);
        $isVerified = $validated['payment_status'] === 'verified';

        $booking = DB::transaction(function () use ($validated, $car, $pricing, $withDriver, $isVerified): Booking {
            $booking = Booking::create([
                'user_id' => $validated['user_id'],
                'car_id' => $car->id,
                'borrower_name' => $validated['borrower_name'],
                'borrower_phone' => $validated['borrower_phone'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $pricing['days'],
                'with_driver' => $withDriver,
                'driver_fee' => $pricing['driver_fee'],
                'total_price' => $pricing['total'],
                'booking_status' => $isVerified ? 'confirmed' : 'pending',
                'payment_deadline' => $isVerified ? null : now()->addMinutes((int) Setting::get('payment_deadline_minutes')),
                'notes' => $validated['notes'] ?? null,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => $validated['payment_method'],
                'amount' => $pricing['total'],
                'payment_status' => $validated['payment_status'],
                'paid_at' => $isVerified ? now() : null,
            ]);

            return $booking;
        });

        return redirect()->route('admin.rentals.show', $booking)->with('success', 'Booking berhasil dibuat.');
    }

    /**
     * Tampilkan detail booking, pembayaran, serah terima, dan pengembalian.
     */
    public function show(Booking $booking): View
    {
        $booking->load(['user', 'car.category', 'payment', 'carReturn']);

        return view('admin.rentals.show', compact('booking'));
    }

    /**
     * Serah terima mobil ke customer (Confirmed -> Ongoing).
     */
    public function handover(Booking $booking): RedirectResponse
    {
        if ($booking->booking_status !== 'confirmed') {
            return back()->with('error', 'Serah terima hanya dapat dilakukan untuk booking berstatus Confirmed.');
        }

        DB::transaction(function () use ($booking): void {
            $booking->update(['booking_status' => 'ongoing']);

            CarReturn::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'condition_description' => 'Mobil diserahkan kepada penyewa dalam kondisi baik.',
                    'late_days' => 0,
                    'late_fee' => 0,
                    'return_status' => 'pending',
                ]
            );
        });

        return back()->with('success', 'Mobil berhasil diserahkan. Status sewa kini Ongoing.');
    }

    /**
     * Pemeriksaan pengembalian: hitung denda keterlambatan, input denda kerusakan (Ongoing -> Completed).
     */
    public function returnCar(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->booking_status !== 'ongoing') {
            return back()->with('error', 'Pengembalian hanya berlaku untuk booking berstatus Ongoing.');
        }

        $validated = $request->validate([
            'actual_return_date' => ['required', 'date'],
            'condition_description' => ['required', 'string', 'max:1000'],
            'damage_fee' => ['nullable', 'numeric', 'min:0', 'max:1000000000'],
            'late_fee_per_day' => ['nullable', 'numeric', 'min:0', 'max:1000000000'],
        ], [
            'actual_return_date.required' => 'Waktu pengembalian aktual wajib diisi.',
            'condition_description.required' => 'Deskripsi kondisi mobil wajib diisi.',
        ]);

        $actual = Carbon::parse($validated['actual_return_date']);
        [$lateDays, $lateFee] = $this->calculateLateFee($booking, $actual, $validated['late_fee_per_day'] ?? null);

        $damageFee = (float) ($validated['damage_fee'] ?? 0);
        $totalFine = $lateFee + $damageFee;

        DB::transaction(function () use ($booking, $actual, $validated, $lateDays, $lateFee, $damageFee, $totalFine): void {
            CarReturn::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'actual_return_date' => $actual,
                    'condition_description' => $validated['condition_description'],
                    'late_days' => $lateDays,
                    'late_fee' => $lateFee,
                    'damage_fee' => $damageFee,
                    'total_fine' => $totalFine,
                    'fine_status' => $totalFine > 0 ? 'unpaid' : 'none',
                    'fine_proof_image' => null,
                    'fine_paid_at' => null,
                    'return_status' => 'completed',
                ]
            );

            $booking->update(['booking_status' => 'completed']);
        });

        return back()->with('success', 'Pengembalian selesai dicatat. Total denda: Rp ' . number_format($totalFine, 0, ',', '.'));
    }

    /**
     * Hitung denda keterlambatan berdasarkan pengaturan sistem (per jam / per hari).
     * Jika petugas mengisi tarif per hari manual, tarif itu dipakai menggantikan pengaturan.
     *
     * @return array{0:int, 1:float} [late_days, late_fee]
     */
    private function calculateLateFee(Booking $booking, Carbon $actual, mixed $manualPerDay): array
    {
        if (! $actual->gt($booking->end_date)) {
            return [0, 0.0];
        }

        $lateMinutes = abs($booking->end_date->diffInMinutes($actual));
        $lateDays = max(1, (int) ceil($lateMinutes / 1440));
        $lateHours = max(1, (int) ceil($lateMinutes / 60));

        if ($manualPerDay !== null && $manualPerDay !== '') {
            return [$lateDays, $lateDays * (float) $manualPerDay];
        }

        if (Setting::get('late_fee_mode') === 'day') {
            return [$lateDays, $lateDays * (float) Setting::get('late_fee_per_day')];
        }

        return [$lateDays, $lateHours * (float) Setting::get('late_fee_per_hour')];
    }

    /**
     * Perbarui status booking secara cepat dari dashboard admin.
     */
    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'booking_status' => ['required', 'in:pending,confirmed,ongoing,completed,cancelled'],
        ]);

        DB::transaction(function () use ($booking, $validated): void {
            $booking->update(['booking_status' => $validated['booking_status']]);

            if ($validated['booking_status'] === 'confirmed' && $booking->payment && $booking->payment->payment_status === 'pending') {
                $booking->payment->update([
                    'payment_status' => 'verified',
                    'paid_at' => $booking->payment->paid_at ?? now(),
                ]);
            }

            if ($validated['booking_status'] === 'ongoing') {
                CarReturn::updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'condition_description' => 'Mobil diserahkan kepada penyewa dalam kondisi baik.',
                        'late_days' => 0,
                        'late_fee' => 0,
                        'return_status' => 'pending',
                    ]
                );
            }
        });

        return back()->with('success', 'Status booking berhasil diperbarui menjadi ' . ucfirst($validated['booking_status']) . '.');
    }

    /**
     * Batalkan booking oleh admin.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        if (! in_array($booking->booking_status, ['pending', 'confirmed'], true)) {
            return back()->with('error', 'Booking yang sedang berjalan atau selesai tidak dapat dibatalkan.');
        }

        $booking->update(['booking_status' => 'cancelled']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
