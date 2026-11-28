@extends('layouts.dashboard')

@section('title', 'Daftar Booking Saya')
@section('page-title', 'Booking Saya')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Booking Saya</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau status pemesanan, pembayaran, dan pengembalian mobil Anda.</p>
        </div>
        <a href="{{ route('customer.rentals.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Sewa Mobil Baru</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if ($bookings->isEmpty())
            <div class="p-12 text-center">
                <i data-lucide="calendar-x" class="w-10 h-10 mx-auto text-slate-300 mb-3"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada booking.</p>
                <a href="{{ route('customer.rentals.create') }}" class="inline-block mt-4 px-5 py-2 bg-blue-600 text-white text-xs font-bold rounded-full">Mulai Sewa Sekarang</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                            <th class="px-6 py-3.5">Mobil</th>
                            <th class="px-6 py-3.5">Tanggal Sewa</th>
                            <th class="px-6 py-3.5">Total Biaya</th>
                            <th class="px-6 py-3.5">Status Sewa</th>
                            <th class="px-6 py-3.5">Pembayaran</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($bookings as $booking)
                            @php
                                $statusMap = [
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'ongoing' => 'bg-indigo-100 text-indigo-800',
                                    'completed' => 'bg-emerald-100 text-emerald-800',
                                    'cancelled' => 'bg-rose-100 text-rose-800',
                                ];
                                $payMap = [
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->name }}" class="w-12 h-10 rounded-lg object-cover bg-slate-100 shrink-0">
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $booking->car->brand }} {{ $booking->car->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $booking->car->plate_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    <p class="font-semibold">{{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</p>
                                    <p class="text-slate-400">{{ $booking->total_days }} hari</p>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 text-xs">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    @if ($booking->carReturn && $booking->carReturn->late_fee > 0)
                                        <span class="block text-[10px] text-rose-600">+ Denda Rp {{ number_format($booking->carReturn->late_fee, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full {{ $statusMap[$booking->booking_status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $booking->booking_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($booking->payment)
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded border {{ $payMap[$booking->payment->payment_status] ?? 'bg-slate-50 text-slate-600' }}">
                                            {{ $booking->payment->payment_status }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('customer.rentals.show', $booking) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-colors">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection
