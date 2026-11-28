@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard Admin</h1>
            <p class="mt-1 text-sm text-slate-500">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>. Berikut ringkasan operasional rental mobil hari ini.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.cars.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition-all">
                <i data-lucide="car" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Kelola Mobil</span>
            </a>
            <a href="{{ route('admin.rentals.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition-all">
                <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-indigo-600"></i>
                <span>Kelola Booking</span>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                <i data-lucide="check-square" class="w-3.5 h-3.5"></i>
                <span>Verifikasi Pembayaran</span>
            </a>
        </div>
    </div>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        {{-- Total Mobil --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i data-lucide="car" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-bold uppercase text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                    {{ $availableCars }} tersedia
                </span>
            </div>
            <p class="text-3xl font-black text-slate-900">{{ $totalCars }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Total Unit Mobil</p>
        </div>

        {{-- Total Customer --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-bold uppercase text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                    Aktif
                </span>
            </div>
            <p class="text-3xl font-black text-slate-900">{{ $totalCustomers }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Customer Terdaftar</p>
        </div>

        {{-- Total Booking --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                </div>
                @if ($pendingPayments > 0)
                    <span class="text-[10px] font-bold uppercase text-amber-700 bg-amber-50 px-2 py-1 rounded-full animate-pulse">
                        {{ $pendingPayments }} menunggu bayar
                    </span>
                @endif
            </div>
            <p class="text-3xl font-black text-slate-900">{{ $totalBookings }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Total Booking</p>
        </div>

        {{-- Total Pendapatan --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-bold uppercase text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full">
                    Verified
                </span>
            </div>
            <p class="text-2xl font-black text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Pendapatan Terverifikasi</p>
        </div>
    </div>

    {{-- Status Booking Grid --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm mb-8">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Breakdown Status Booking</h2>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="text-center p-3 rounded-xl bg-amber-50 border border-amber-100">
                <p class="text-2xl font-black text-amber-700">{{ $bookingsByStatus['pending'] }}</p>
                <p class="text-[10px] font-bold uppercase text-amber-600 mt-1">Pending</p>
            </div>
            <div class="text-center p-3 rounded-xl bg-blue-50 border border-blue-100">
                <p class="text-2xl font-black text-blue-700">{{ $bookingsByStatus['confirmed'] }}</p>
                <p class="text-[10px] font-bold uppercase text-blue-600 mt-1">Confirmed</p>
            </div>
            <div class="text-center p-3 rounded-xl bg-indigo-50 border border-indigo-100">
                <p class="text-2xl font-black text-indigo-700">{{ $bookingsByStatus['ongoing'] }}</p>
                <p class="text-[10px] font-bold uppercase text-indigo-600 mt-1">Ongoing</p>
            </div>
            <div class="text-center p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                <p class="text-2xl font-black text-emerald-700">{{ $bookingsByStatus['completed'] }}</p>
                <p class="text-[10px] font-bold uppercase text-emerald-600 mt-1">Completed</p>
            </div>
            <div class="text-center p-3 rounded-xl bg-rose-50 border border-rose-100">
                <p class="text-2xl font-black text-rose-700">{{ $bookingsByStatus['cancelled'] }}</p>
                <p class="text-[10px] font-bold uppercase text-rose-600 mt-1">Cancelled</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Booking Terbaru --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Booking Terbaru</h2>
                <span class="text-[10px] font-semibold text-slate-400 uppercase">5 Terakhir</span>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse ($recentBookings as $booking)
                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50/50 transition-colors">
                        {{-- Car Image --}}
                        <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                            <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->name }}" class="w-full h-full object-cover">
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $booking->car->name }}</p>
                            <p class="text-xs text-slate-500">{{ $booking->user->name }} &middot; {{ $booking->start_date->format('d M Y') }}</p>
                        </div>
                        {{-- Status Badge --}}
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'ongoing' => 'bg-indigo-100 text-indigo-800',
                                'completed' => 'bg-emerald-100 text-emerald-800',
                                'cancelled' => 'bg-rose-100 text-rose-800',
                            ];

                            $isUnpaid = $booking->booking_status === 'pending' && !$booking->payment;
                            $badgeColor = $isUnpaid
                                ? 'bg-slate-100 text-slate-600'
                                : ($statusColors[$booking->booking_status] ?? 'bg-slate-100 text-slate-600');
                            $badgeLabel = $isUnpaid ? 'Belum Bayar' : $booking->booking_status;
                        @endphp
                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $badgeColor }}">
                            {{ $badgeLabel }}
                        </span>
                        @if (($booking->booking_status === 'pending' && $booking->payment) || $booking->booking_status === 'confirmed')
                            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="shrink-0">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="booking_status" value="{{ $booking->booking_status === 'pending' ? 'confirmed' : 'ongoing' }}">
                                <button type="submit" class="rounded-lg bg-blue-50 px-3 py-2 text-[10px] font-bold text-blue-700 hover:bg-blue-100">
                                    {{ $booking->booking_status === 'pending' ? 'Konfirmasi' : 'Mulai Rental' }}
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                        <p class="text-sm text-slate-400 font-medium">Belum ada booking</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Mobil Sedang Disewa --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Mobil Sedang Disewa</h2>
                <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">
                    {{ $ongoingRentals->count() }} aktif
                </span>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse ($ongoingRentals as $rental)
                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50/50 transition-colors">
                        {{-- Car Image --}}
                        <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                            <img src="{{ $rental->car->image_url }}" alt="{{ $rental->car->name }}" class="w-full h-full object-cover">
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $rental->car->name }}</p>
                            <p class="text-xs text-slate-500">{{ $rental->car->plate_number }}</p>
                        </div>
                        {{-- Customer & Dates --}}
                        <div class="text-right shrink-0">
                            <p class="text-xs font-semibold text-slate-700">{{ $rental->user->name }}</p>
                            <p class="text-[10px] text-slate-400">s/d {{ $rental->end_date->format('d M Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <i data-lucide="car" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                        <p class="text-sm text-slate-400 font-medium">Semua mobil tersedia di garasi</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection