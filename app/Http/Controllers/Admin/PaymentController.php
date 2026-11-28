<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Tampilkan daftar pembayaran untuk diverifikasi admin.
     */
    public function index(Request $request): View
    {
        Booking::expireOverdue();

        $status = $request->query('status');

        $payments = Payment::with(['booking.user', 'booking.car'])
            ->when($status, fn ($q) => $q->where('payment_status', $status))
            ->latest()
            ->paginate(12);

        return view('admin.payments.index', compact('payments', 'status'));
    }

    /**
     * Verifikasi pembayaran customer dan konfirmasi booking.
     */
    public function verify(Payment $payment): RedirectResponse
    {
        if ($payment->payment_status === 'verified') {
            return back()->with('error', 'Pembayaran ini sudah diverifikasi sebelumnya.');
        }

        if ($payment->booking && $payment->booking->booking_status === 'cancelled') {
            return back()->with('error', 'Booking ini sudah dibatalkan, pembayaran tidak dapat diverifikasi.');
        }

        DB::transaction(function () use ($payment): void {
            $payment->update([
                'payment_status' => 'verified',
                'paid_at' => $payment->paid_at ?? now(),
            ]);

            if ($payment->booking && $payment->booking->booking_status === 'pending') {
                $payment->booking->update([
                    'booking_status' => 'confirmed',
                ]);
            }
        });

        return back()->with('success', 'Pembayaran diverifikasi dan status booking menjadi Confirmed.');
    }

    /**
     * Tolak bukti pembayaran customer.
     */
    public function reject(Payment $payment): RedirectResponse
    {
        if ($payment->payment_status === 'verified') {
            return back()->with('error', 'Pembayaran yang sudah terverifikasi tidak dapat ditolak.');
        }

        DB::transaction(function () use ($payment): void {
            $payment->update(['payment_status' => 'rejected']);

            // Beri waktu baru agar customer sempat mengunggah ulang bukti yang benar.
            if ($payment->booking && $payment->booking->booking_status === 'pending') {
                $payment->booking->update([
                    'payment_deadline' => now()->addMinutes((int) \App\Models\Setting::get('payment_deadline_minutes')),
                ]);
            }
        });

        return back()->with('success', 'Pembayaran ditandai sebagai Ditolak (Rejected). Customer dapat mengunggah ulang bukti.');
    }
}
