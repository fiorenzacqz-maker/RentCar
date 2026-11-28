<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\CarReturn;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard petugas: fokus pada antrean kerja harian.
     */
    public function index(): View
    {
        Booking::expireOverdue();

        return view('staff.dashboard', [
            'pendingPayments' => Payment::where('payment_status', 'pending')->whereNotNull('proof_image')->count(),
            'readyForHandover' => Booking::where('booking_status', 'confirmed')->count(),
            'ongoingCount' => Booking::where('booking_status', 'ongoing')->count(),
            'waitingFines' => CarReturn::where('fine_status', 'waiting')->count(),
            'availableCars' => Car::where('status', 'available')->count(),
            'totalCars' => Car::count(),
            'handoverQueue' => Booking::with(['user', 'car'])->where('booking_status', 'confirmed')->orderBy('start_date')->take(6)->get(),
            'ongoingRentals' => Booking::with(['user', 'car'])->where('booking_status', 'ongoing')->orderBy('end_date')->take(8)->get(),
        ]);
    }
}
