@extends('layouts.dashboard')

@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Verifikasi Pembayaran Customer</h1>
        <p class="mt-1 text-sm text-slate-500">Periksa bukti transfer dan konfirmasi pembayaran agar booking siap diserahkan.</p>
    </div>

    <div class="flex flex-wrap gap-2 mb-5">
        <a href="{{ route('admin.payments.index') }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold {{ !$status ? 'bg-[#0f2557] text-white' : 'bg-white border border-slate-200 text-slate-600' }}">Semua</a>
        @foreach (['pending', 'verified', 'rejected'] as $st)
            <a href="{{ route('admin.payments.index', ['status' => $st]) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase {{ $status === $st ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                {{ $st }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-3.5">Booking & Customer</th>
                        <th class="px-6 py-3.5">Metode & Jumlah</th>
                        <th class="px-6 py-3.5">Bukti Bayar</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.rentals.show', $payment->booking) }}" class="font-bold text-blue-600 hover:underline">
                                    #{{ $payment->booking_id }} &middot; {{ $payment->booking->user->name ?? '-' }}
                                </a>
                                <p class="text-xs text-slate-400">{{ $payment->booking->car->brand ?? '' }} {{ $payment->booking->car->name ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <p class="font-bold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                <p class="text-slate-400">{{ $payment->payment_method }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if ($payment->proof_image)
                                    <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:underline">
                                        <i data-lucide="image" class="w-4 h-4"></i> Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">Belum diunggah</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full
                                    {{ $payment->payment_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : ($payment->payment_status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $payment->payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                @if ($payment->payment_status === 'pending')
                                    <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg cursor-pointer">Verify</button>
                                    </form>
                                    <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg cursor-pointer">Reject</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.rentals.show', $payment->booking) }}" class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">Detail</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">Belum ada data pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $payments->withQueryString()->links() }}
        </div>
    </div>
@endsection
