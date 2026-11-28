@extends('layouts.dashboard')

@section('title', 'Pembayaran Saya')
@section('page-title', 'Pembayaran')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pembayaran & Upload Bukti</h1>
        <p class="mt-1 text-sm text-slate-500">Unggah bukti pembayaran untuk booking Anda yang berstatus pending agar segera diverifikasi admin.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if ($bookings->isEmpty())
            <div class="p-12 text-center">
                <i data-lucide="wallet" class="w-10 h-10 mx-auto text-slate-300 mb-3"></i>
                <p class="text-sm font-semibold text-slate-500">Belum ada tagihan pembayaran.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($bookings as $booking)
                    <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->name }}" class="w-16 h-12 rounded-xl object-cover bg-slate-100 shrink-0">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Booking #{{ $booking->id }} &middot; {{ $booking->car->brand }} {{ $booking->car->name }}</p>
                                <p class="text-xs text-slate-500">{{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }} ({{ $booking->total_days }} hari)</p>
                                <p class="text-sm font-black text-[#0f2557] mt-1">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            @if ($booking->payment)
                                <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full w-fit
                                    {{ $booking->payment->payment_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : ($booking->payment->payment_status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $booking->payment->payment_status }}
                                </span>
                            @endif

                            @if ($booking->booking_status === 'pending' && (! $booking->payment || $booking->payment->payment_status !== 'verified'))
                                <form action="{{ route('customer.payments.store', $booking) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    <select name="payment_method" class="px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs">
                                        <option value="Transfer Bank">Transfer Bank</option>
                                        <option value="E-Wallet">E-Wallet</option>
                                        <option value="Tunai">Tunai</option>
                                    </select>
                                    <input type="file" name="proof_image" accept="image/*" required class="text-xs max-w-[180px]">
                                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg cursor-pointer">
                                        Upload
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('customer.rentals.show', $booking) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg">
                                    Lihat Detail
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
