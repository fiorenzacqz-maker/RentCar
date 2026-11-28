@extends('layouts.dashboard')

@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard Petugas')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Halo, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">Antrean kerja operasional hari ini.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach ([
            ['Pembayaran Perlu Verifikasi', $pendingPayments, route('admin.payments.index', ['status' => 'pending']), 'text-amber-600'],
            ['Siap Serah Terima', $readyForHandover, route('admin.rentals.index', ['status' => 'confirmed']), 'text-indigo-600'],
            ['Sedang Disewa', $ongoingCount, route('admin.rentals.index', ['status' => 'ongoing']), 'text-blue-600'],
            ['Denda Perlu Verifikasi', $waitingFines, route('admin.fines.index', ['status' => 'waiting']), 'text-rose-600'],
        ] as [$label, $value, $url, $color])
            <a href="{{ $url }}" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-shadow">
                <span class="block text-[11px] text-slate-400 uppercase font-bold">{{ $label }}</span>
                <span class="block mt-1 text-3xl font-black {{ $color }}">{{ $value }}</span>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Antrean Serah Terima</h2>
            @forelse ($handoverQueue as $b)
                <a href="{{ route('admin.rentals.show', $b) }}" class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0 text-xs hover:bg-slate-50">
                    <span><strong>#{{ $b->id }}</strong> {{ $b->car->brand }} {{ $b->car->name }} &middot; {{ $b->borrower_name ?? $b->user->name }}</span>
                    <span class="text-slate-500">{{ $b->start_date->format('d M H:i') }}</span>
                </a>
            @empty
                <p class="text-xs text-slate-400">Tidak ada antrean.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Mobil Sedang Disewa</h2>
            @forelse ($ongoingRentals as $b)
                <a href="{{ route('admin.rentals.show', $b) }}" class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0 text-xs hover:bg-slate-50">
                    <span><strong>#{{ $b->id }}</strong> {{ $b->car->brand }} {{ $b->car->name }} &middot; {{ $b->borrower_name ?? $b->user->name }}</span>
                    <span class="{{ $b->end_date->isPast() ? 'text-rose-600 font-bold' : 'text-slate-500' }}">kembali {{ $b->end_date->format('d M H:i') }}</span>
                </a>
            @empty
                <p class="text-xs text-slate-400">Tidak ada mobil yang sedang disewa.</p>
            @endforelse
        </div>
    </div>

    <p class="mt-6 text-xs text-slate-400">Armada tersedia: {{ $availableCars }} dari {{ $totalCars }} unit.</p>
@endsection
