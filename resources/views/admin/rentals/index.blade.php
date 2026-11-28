@extends('layouts.dashboard')

@section('title', 'Kelola Booking & Pengembalian')
@section('page-title', 'Booking & Pengembalian')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Booking, Serah Terima & Pengembalian</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola seluruh transaksi sewa mobil, penyerahan unit, serta pencatatan pengembalian & denda.</p>
        </div>
        <a href="{{ route('admin.rentals.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Buat Booking Baru</span>
        </a>
    </div>

    {{-- Filter Status --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <a href="{{ route('admin.rentals.index') }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold {{ !$status ? 'bg-[#0f2557] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua</a>
        @foreach (['pending', 'confirmed', 'ongoing', 'completed', 'cancelled'] as $st)
            <a href="{{ route('admin.rentals.index', ['status' => $st]) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase {{ $status === $st ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                {{ $st }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-3.5">#ID / Customer</th>
                        <th class="px-6 py-3.5">Mobil</th>
                        <th class="px-6 py-3.5">Jadwal Sewa</th>
                        <th class="px-6 py-3.5">Total Biaya</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($bookings as $booking)
                        @php
                            $statusMap = [
                                'pending' => 'bg-amber-100 text-amber-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'ongoing' => 'bg-indigo-100 text-indigo-800',
                                'completed' => 'bg-emerald-100 text-emerald-800',
                                'cancelled' => 'bg-rose-100 text-rose-800',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">#{{ $booking->id }} &middot; {{ $booking->user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $booking->user->phone }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-xs">{{ $booking->car->brand }} {{ $booking->car->name }}</p>
                                <p class="text-[11px] font-mono text-slate-400">{{ $booking->car->plate_number }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <p class="font-semibold">{{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</p>
                                <p class="text-slate-400">{{ $booking->total_days }} hari</p>
                            </td>
                            <td class="px-6 py-4 text-xs font-black text-[#0f2557]">
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
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.rentals.show', $booking) }}" class="inline-block px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg">
                                    Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">Tidak ada data booking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $bookings->withQueryString()->links() }}
        </div>
    </div>
@endsection
