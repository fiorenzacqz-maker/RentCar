<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard customer dengan riwayat booking dan ringkasan pribadi.
     */
    public function index(Request $request): View
    {
        Booking::expireOverdue();

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Statistik pribadi customer
        $totalBookings = $user->bookings()->count();
        $activeBookings = $user->bookings()
            ->whereIn('booking_status', ['pending', 'confirmed', 'ongoing'])
            ->count();
        $completedBookings = $user->bookings()
            ->where('booking_status', 'completed')
            ->count();
        $totalSpent = $user->bookings()
            ->whereHas('payment', fn ($query) => $query->where('payment_status', 'verified'))
            ->sum('total_price');

        // Booking aktif (pending, confirmed, ongoing)
        $currentBookings = $user->bookings()
            ->with(['car', 'payment'])
            ->whereIn('booking_status', ['pending', 'confirmed', 'ongoing'])
            ->latest()
            ->get();

        // Riwayat rental sebelumnya (completed, cancelled)
        $pastBookings = $user->bookings()
            ->with(['car', 'payment', 'carReturn'])
            ->whereIn('booking_status', ['completed', 'cancelled'])
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact(
            'user',
            'totalBookings',
            'activeBookings',
            'completedBookings',
            'totalSpent',
            'currentBookings',
            'pastBookings'
        ));
    }
}
