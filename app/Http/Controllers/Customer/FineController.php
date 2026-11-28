<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\PublicStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    /**
     * Daftar denda (keterlambatan/kerusakan) milik customer yang sedang login.
     */
    public function index(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['car', 'carReturn'])
            ->whereHas('carReturn', fn ($q) => $q->where('total_fine', '>', 0))
            ->latest()
            ->get();

        return view('customer.fines.index', [
            'bookings' => $bookings,
            'bankInfo' => (string) \App\Models\Setting::get('bank_info'),
            'qrisUrl' => \App\Models\Setting::qrisUrl(),
        ]);
    }

    /**
     * Unggah bukti pelunasan denda; menunggu verifikasi petugas.
     */
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        // Cegah IDOR: hanya pemilik booking.
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        $return = $booking->carReturn;

        if (! $return || (float) $return->total_fine <= 0) {
            return back()->with('error', 'Booking ini tidak memiliki denda.');
        }

        if ($return->fine_status === 'paid') {
            return back()->with('error', 'Denda sudah lunas.');
        }

        if ($return->fine_status === 'waiting') {
            return back()->with('error', 'Bukti pelunasan sedang menunggu verifikasi petugas.');
        }

        $validated = $request->validate([
            'fine_payment_method' => ['required', 'in:Transfer Bank,E-Wallet,Tunai'],
            'fine_proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'fine_proof_image.required' => 'Bukti pembayaran denda wajib diunggah.',
            'fine_proof_image.image' => 'File bukti harus berupa gambar.',
            'fine_proof_image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        PublicStorageService::delete($return->fine_proof_image);

        $return->update([
            'fine_payment_method' => $validated['fine_payment_method'],
            'fine_proof_image' => PublicStorageService::store($request->file('fine_proof_image'), 'fines'),
            'fine_status' => 'waiting',
        ]);

        return back()->with('success', 'Bukti pelunasan denda terkirim dan menunggu verifikasi petugas.');
    }
}
