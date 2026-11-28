<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PublicStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Tampilkan daftar tagihan & riwayat pembayaran milik customer.
     */
    public function index(Request $request): View
    {
        Booking::expireOverdue();

        $bookings = $request->user()
            ->bookings()
            ->with(['car', 'payment'])
            ->where('booking_status', '!=', 'cancelled')
            ->latest()
            ->get();

        return view('customer.payments.index', compact('bookings'));
    }

    /**
     * Unggah atau perbarui bukti pembayaran untuk booking tertentu.
     */
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        Booking::expireOverdue();
        $booking->refresh();

        if ($booking->booking_status === 'cancelled') {
            return back()->with('error', 'Booking dibatalkan karena melewati tenggat pembayaran atau telah dibatalkan.');
        }

        if ($booking->booking_status !== 'pending') {
            return back()->with('error', 'Pembayaran hanya dapat diunggah untuk booking berstatus pending.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:Transfer Bank,E-Wallet,Tunai'],
            'proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'proof_image.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_image.image' => 'File bukti harus berupa gambar.',
            'proof_image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $payment = $booking->payment;
        if ($payment && $payment->proof_image) {
            PublicStorageService::delete($payment->proof_image);
        }

        $proofPath = PublicStorageService::store($request->file('proof_image'), 'payments');

        Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'payment_method' => $validated['payment_method'],
                'amount' => $booking->total_price,
                'proof_image' => $proofPath,
                'payment_status' => 'pending',
                'paid_at' => now(),
            ]
        );

        return back()->with('success', 'Bukti pembayaran berhasil diunggah dan menunggu verifikasi admin.');
    }
}
