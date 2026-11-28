@extends('layouts.dashboard')

@section('title', 'Denda & Pelunasan')
@section('page-title', 'Denda')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Denda & Pelunasan</h1>
        <p class="mt-1 text-sm text-slate-500">Rincian denda keterlambatan/kerusakan dari pengembalian mobil Anda. Unggah bukti pembayaran untuk diverifikasi petugas.</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    @forelse ($bookings as $booking)
        @php $ret = $booking->carReturn; @endphp
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm mb-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <div>
                    <a href="{{ route('customer.rentals.show', $booking) }}" class="text-sm font-black text-slate-900 hover:text-blue-600">
                        Booking #{{ $booking->id }} &middot; {{ $booking->car->brand }} {{ $booking->car->name }}
                    </a>
                    <p class="text-xs text-slate-500">{{ $booking->start_date->format('d M Y H:i') }} &rarr; {{ $booking->end_date->format('d M Y H:i') }}</p>
                </div>
                <span class="text-[11px] font-bold uppercase px-3 py-1 rounded-full w-fit {{ $ret->fine_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($ret->fine_status === 'waiting' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                    {{ ['unpaid' => 'Belum Dibayar', 'waiting' => 'Menunggu Verifikasi', 'paid' => 'Lunas', 'none' => '-'][$ret->fine_status] }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block">Keterlambatan ({{ $ret->late_days }} hari)</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($ret->late_fee, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Kerusakan</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($ret->damage_fee, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Total Denda</span>
                    <span class="font-black text-rose-600 text-sm">Rp {{ number_format($ret->total_fine, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Dibayar Pada</span>
                    <span class="font-bold text-slate-800">{{ $ret->fine_paid_at ? $ret->fine_paid_at->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>
            @if ($ret->condition_description)
                <p class="mt-3 text-xs text-slate-600"><strong>Catatan pemeriksaan:</strong> {{ $ret->condition_description }}</p>
            @endif

            @if ($ret->fine_status === 'unpaid')
                <div class="mt-5 pt-5 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-100 text-xs text-slate-600 space-y-1">
                        <p class="font-bold text-blue-800">Info Pembayaran</p>
                        <p class="whitespace-pre-line">{{ $bankInfo }}</p>
                        <img src="{{ $qrisUrl }}" alt="QRIS" class="mt-2 w-28 h-28 object-contain bg-white rounded-lg border border-slate-200">
                    </div>
                    <form action="{{ route('customer.fines.store', $booking) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                            <select name="fine_payment_method" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="E-Wallet">E-Wallet / QRIS</option>
                                <option value="Tunai">Tunai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Bukti Pembayaran Denda *</label>
                            <input type="file" name="fine_proof_image" accept="image/*" required
                                class="w-full px-2.5 py-2 border border-slate-200 rounded-xl text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl cursor-pointer">Kirim Bukti Pelunasan</button>
                    </form>
                </div>
            @elseif ($ret->fine_status === 'waiting')
                <div class="mt-5 pt-5 border-t border-slate-100 flex items-center gap-4">
                    @if ($ret->fine_proof_image)
                        <a href="{{ asset('storage/' . $ret->fine_proof_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $ret->fine_proof_image) }}" alt="Bukti" class="w-24 h-24 object-cover rounded-xl border border-slate-200">
                        </a>
                    @endif
                    <p class="text-xs text-amber-700">Bukti pelunasan sudah dikirim dan sedang diperiksa petugas.</p>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center text-sm text-slate-400 shadow-sm">
            Anda tidak memiliki denda. Terima kasih telah mengembalikan mobil tepat waktu!
        </div>
    @endforelse
@endsection
