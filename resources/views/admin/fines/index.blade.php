@extends('layouts.dashboard')

@section('title', 'Verifikasi Denda')
@section('page-title', 'Denda')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pelunasan Denda</h1>
        <p class="mt-1 text-sm text-slate-500">Verifikasi bukti pembayaran denda keterlambatan/kerusakan dari customer.</p>
    </div>

    <div class="mb-5 flex flex-wrap gap-2 text-xs">
        @foreach (['waiting' => 'Menunggu Verifikasi', 'unpaid' => 'Belum Dibayar', 'paid' => 'Lunas', 'all' => 'Semua'] as $value => $label)
            <a href="{{ route('admin.fines.index', ['status' => $value]) }}" class="px-3 py-1.5 rounded-lg font-bold {{ $status === $value ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3">Booking</th>
                    <th class="text-left px-4 py-3">Customer</th>
                    <th class="text-right px-4 py-3">Telat</th>
                    <th class="text-right px-4 py-3">Kerusakan</th>
                    <th class="text-right px-4 py-3">Total</th>
                    <th class="text-left px-4 py-3">Bukti</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($fines as $fine)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.rentals.show', $fine->booking) }}" class="font-bold text-blue-600 hover:underline">#{{ $fine->booking_id }}</a>
                            <div class="text-slate-500">{{ $fine->booking->car->brand }} {{ $fine->booking->car->name }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $fine->booking->user->name }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($fine->late_fee, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($fine->damage_fee, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-rose-600">Rp {{ number_format($fine->total_fine, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if ($fine->fine_proof_image)
                                <a href="{{ asset('storage/' . $fine->fine_proof_image) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                                <span class="text-slate-400">({{ $fine->fine_payment_method }})</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold uppercase {{ $fine->fine_status === 'paid' ? 'text-emerald-600' : ($fine->fine_status === 'waiting' ? 'text-amber-600' : 'text-rose-600') }}">
                            {{ ['unpaid' => 'Belum Dibayar', 'waiting' => 'Menunggu', 'paid' => 'Lunas'][$fine->fine_status] ?? $fine->fine_status }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if ($fine->fine_status !== 'paid')
                                <form action="{{ route('admin.fines.verify', $fine) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg cursor-pointer">{{ $fine->fine_status === 'waiting' ? 'Verifikasi' : 'Tandai Lunas (Tunai)' }}</button>
                                </form>
                            @endif
                            @if ($fine->fine_status === 'waiting')
                                <form action="{{ route('admin.fines.reject', $fine) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-bold rounded-lg cursor-pointer">Tolak</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">Tidak ada data denda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $fines->links() }}</div>
@endsection
