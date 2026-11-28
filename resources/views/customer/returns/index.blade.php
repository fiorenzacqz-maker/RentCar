@extends('layouts.dashboard')

@section('title', 'Pengembalian Mobil')
@section('page-title', 'Pengembalian Mobil')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Status Pengembalian Mobil</h1>
        <p class="mt-1 text-sm text-slate-500">Pantau jadwal batas pengembalian, kondisi unit, dan denda keterlambatan (jika ada).</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if ($bookings->isEmpty())
            <div class="p-12 text-center">
                <i data-lucide="car" class="w-10 h-10 mx-auto text-slate-300 mb-3"></i>
                <p class="text-sm font-semibold text-slate-500">Belum ada mobil yang sedang disewa atau telah dikembalikan.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($bookings as $booking)
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->name }}" class="w-16 h-12 rounded-xl object-cover bg-slate-100 shrink-0">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $booking->car->brand }} {{ $booking->car->name }} ({{ $booking->car->plate_number }})</p>
                                <p class="text-xs text-slate-500">Batas Kembali: <strong>{{ $booking->end_date->format('d M Y') }}</strong></p>
                                @if ($booking->carReturn)
                                    <p class="text-xs text-slate-600 mt-1">{{ $booking->carReturn->condition_description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            @if ($booking->carReturn && $booking->carReturn->return_status === 'completed')
                                <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">Sudah Dikembalikan</span>
                                <p class="text-xs font-bold mt-1 {{ $booking->carReturn->late_fee > 0 ? 'text-rose-600' : 'text-slate-600' }}">
                                    Denda: Rp {{ number_format($booking->carReturn->late_fee, 0, ',', '.') }}
                                </p>
                            @else
                                <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800">Sedang Disewa</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
