@extends('layouts.dashboard')

@section('title', 'Dashboard Customer')
@section('page-title', 'Dashboard Customer')

@section('content')
    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Halo, {{ $user->name }}!</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola dan pantau status rental mobil Anda dari sini.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('customer.rentals.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                <span>Sewa Mobil Baru</span>
            </a>
            <a href="{{ route('customer.returns.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                <i data-lucide="corner-down-left" class="w-3.5 h-3.5"></i>
                <span>Pengembalian Mobil</span>
            </a>
        </div>
    </div>

    {{-- Statistik Pribadi --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Booking --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
                <i data-lucide="calendar" class="w-5 h-5"></i>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ $totalBookings }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Total Booking</p>
        </div>

        {{-- Booking Aktif --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ $activeBookings }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Booking Aktif</p>
        </div>

        {{-- Selesai --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ $completedBookings }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Rental Selesai</p>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-3">
                <i data-lucide="wallet" class="w-5 h-5"></i>
            </div>
            <p class="text-lg font-black text-slate-900">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Total Pengeluaran</p>
        </div>
    </div>

    {{-- Booking Aktif Saat Ini --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Booking Aktif Saat Ini</h2>
            @if ($activeBookings > 0)
                <span class="text-[10px] font-bold uppercase text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full animate-pulse">
                    {{ $activeBookings }} aktif
                </span>
            @endif
        </div>

        @if ($currentBookings->isEmpty())
            <div class="px-6 py-12 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                    <i data-lucide="calendar-x" class="w-7 h-7 text-slate-400"></i>
                </div>
                <p class="text-sm font-semibold text-slate-500">Anda belum memiliki booking aktif</p>
                <p class="text-xs text-slate-400 mt-1">Jelajahi katalog mobil dan mulai sewa sekarang!</p>
                <a href="{{ route('homepage') }}#katalog" class="inline-flex items-center gap-1.5 mt-4 px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-md transition-all">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    Lihat Daftar Mobil
                </a>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach ($currentBookings as $booking)
                    <div class="px-6 py-5">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Car Image --}}
                            <div class="w-20 h-16 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                                <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->name }}" class="w-full h-full object-cover">
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm font-bold text-slate-800">{{ $booking->car->name }}</h3>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-amber-100 text-amber-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'ongoing' => 'bg-indigo-100 text-indigo-800',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Menunggu Pembayaran',
                                            'confirmed' => 'Dikonfirmasi',
                                            'ongoing' => 'Sedang Disewa',
                                        ];

                                        $badgeColor = $statusColors[$booking->booking_status] ?? 'bg-slate-100 text-slate-600';
                                        $badgeLabel = $statusLabels[$booking->booking_status] ?? $booking->booking_status;

                                        if ($booking->booking_status === 'pending' && $booking->payment) {
                                            if ($booking->payment->payment_status === 'pending') {
                                                $badgeColor = 'bg-sky-100 text-sky-800';
                                                $badgeLabel = 'Menunggu Verifikasi';
                                            } elseif ($booking->payment->payment_status === 'rejected') {
                                                $badgeColor = 'bg-rose-100 text-rose-800';
                                                $badgeLabel = 'Pembayaran Ditolak';
                                            }
                                        }
                                    @endphp
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $badgeColor }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ $booking->start_date->format('d M') }} - {{ $booking->end_date->format('d M Y') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        {{ $booking->total_days }} hari
                                    </span>
                                    <span class="font-bold text-slate-700">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Payment Status & Tombol Bayar Sekarang + Batalkan --}}
                            <div class="sm:text-right shrink-0 flex flex-col sm:items-end gap-2">
                                @if ($booking->payment)
                                    @php
                                        $payColors = [
                                            'pending' => 'text-amber-600',
                                            'verified' => 'text-emerald-600',
                                            'paid' => 'text-emerald-600',
                                            'rejected' => 'text-rose-600',
                                        ];
                                        $payLabels = [
                                            'pending' => 'Menunggu Verifikasi',
                                            'verified' => 'Pembayaran Terverifikasi',
                                            'paid' => 'Pembayaran Terverifikasi',
                                            'rejected' => 'Pembayaran Ditolak',
                                        ];
                                    @endphp
                                    <div>
                                        <p class="text-[10px] font-bold uppercase {{ $payColors[$booking->payment->payment_status] ?? 'text-slate-500' }}">
                                            {{ $payLabels[$booking->payment->payment_status] ?? $booking->payment->payment_status }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">{{ $booking->payment->payment_method }}</p>
                                    </div>

                                    @if ($booking->payment->payment_status === 'rejected')
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('customer.payments.create', $booking->id) }}"
                                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                                                <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                                                <span>Bayar Ulang</span>
                                            </a>
                                            <form action="{{ route('customer.bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer">
                                                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                                    <span>Batalkan</span>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @elseif ($booking->booking_status === 'pending')
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('customer.payments.create', $booking->id) }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                                            <span>Bayar Sekarang</span>
                                        </a>
                                        <form action="{{ route('customer.bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer">
                                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                                <span>Batalkan</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Riwayat Booking --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Riwayat Rental</h2>
            <span class="text-[10px] font-semibold text-slate-400 uppercase">5 Terakhir</span>
        </div>

        @if ($pastBookings->isEmpty())
            <div class="px-6 py-10 text-center">
                <i data-lucide="history" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                <p class="text-sm text-slate-400 font-medium">Belum ada riwayat rental</p>
            </div>
        @else
            {{-- Table Desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Mobil</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Durasi</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($pastBookings as $booking)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                            <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $booking->car->name }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $booking->car->plate_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-xs text-slate-600">
                                    {{ $booking->start_date->format('d M') }} - {{ $booking->end_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3 text-xs text-slate-600">{{ $booking->total_days }} hari</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-800">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $pastStatusColors = [
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'cancelled' => 'bg-rose-100 text-rose-800',
                                        ];
                                        $pastStatusLabels = [
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                    @endphp
                                    <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $pastStatusColors[$booking->booking_status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $pastStatusLabels[$booking->booking_status] ?? $booking->booking_status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Cards Mobile --}}
            <div class="sm:hidden divide-y divide-slate-50">
                @foreach ($pastBookings as $booking)
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-800">{{ $booking->car->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $booking->start_date->format('d M') }} - {{ $booking->end_date->format('d M Y') }}</p>
                            </div>
                            <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $pastStatusColors[$booking->booking_status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $pastStatusLabels[$booking->booking_status] ?? $booking->booking_status }}
                            </span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-500">
                            <span>{{ $booking->total_days }} hari</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection