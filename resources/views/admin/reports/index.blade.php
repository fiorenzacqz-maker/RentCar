@extends('layouts.dashboard')

@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laporan Transaksi & Pendapatan</h1>
        <p class="mt-1 text-sm text-slate-500">Filter berdasarkan tanggal order, lalu ekspor CSV atau cetak.</p>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-5 bg-white rounded-2xl border border-slate-100 p-4 shadow-sm flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Dari</label>
            <input type="date" name="from" value="{{ $from }}" class="px-3 py-2 border border-slate-200 rounded-xl text-xs">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Sampai</label>
            <input type="date" name="to" value="{{ $to }}" class="px-3 py-2 border border-slate-200 rounded-xl text-xs">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Booking</label>
            <select name="status" class="px-3 py-2 border border-slate-200 rounded-xl text-xs">
                <option value="">Semua</option>
                @foreach (['pending', 'confirmed', 'ongoing', 'completed', 'cancelled'] as $st)
                    <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-xl cursor-pointer">Terapkan</button>
    </form>

    @include('admin.reports._summary', ['summary' => $summary])

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-x-auto">
        @include('admin.reports._table', ['bookings' => $bookings])
    </div>
    <div class="mt-4">{{ $bookings->links() }}</div>
@endsection
