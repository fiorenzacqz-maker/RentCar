<?php

use App\Models\Booking;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan otomatis booking pending yang melewati tenggat pembayaran.
Artisan::command('bookings:expire', function () {
    $count = Booking::expireOverdue();
    $this->info("{$count} booking melewati tenggat bayar dan dibatalkan.");
})->purpose('Batalkan booking yang melewati tenggat pembayaran');

Schedule::command('bookings:expire')->everyMinute();
