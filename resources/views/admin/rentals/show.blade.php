@extends('layouts.dashboard')

@section('title', 'Kelola Booking #' . $booking->id)
@section('page-title', 'Detail Booking #' . $booking->id)

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('admin.rentals.index') }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Daftar Booking
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Booking #{{ $booking->id }}</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($booking->booking_status === 'confirmed')
                <form action="{{ route('admin.rentals.handover', $booking) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm cursor-pointer">
                        Serahkan Mobil (Mulai Sewa)
                    </button>
                </form>
            @endif

            @if (in_array($booking->booking_status, ['pending', 'confirmed'], true))
                <form action="{{ route('admin.rentals.cancel', $booking) }}" method="POST" onsubmit="return confirm('Batalkan booking ini?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 cursor-pointer">
                        Batalkan Booking
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Customer & Mobil --}}
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Informasi Sewa</h2>
                    <span class="text-xs font-bold uppercase px-3 py-1 rounded-full bg-slate-100 text-slate-800">{{ $booking->booking_status }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-slate-400 uppercase font-bold block mb-1">Penyewa</span>
                        <p class="font-bold text-sm text-slate-900">{{ $booking->user->name }}</p>
                        <p class="text-slate-600">{{ $booking->user->email }}</p>
                        <p class="text-slate-600">{{ $booking->user->phone }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-slate-400 uppercase font-bold block mb-1">Unit Mobil</span>
                        <p class="font-bold text-sm text-slate-900">{{ $booking->car->brand }} {{ $booking->car->name }}</p>
                        <p class="text-slate-600 font-mono">{{ $booking->car->plate_number }}</p>
                        <p class="text-slate-600">Rp {{ number_format($booking->car->price_per_day, 0, ',', '.') }} / hari</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-2 text-xs">
                    <div>
                        <span class="text-slate-400 block">Mulai</span>
                        <span class="font-bold text-slate-800">{{ $booking->start_date->format('d M Y, H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Batas Kembali</span>
                        <span class="font-bold text-slate-800">{{ $booking->end_date->format('d M Y, H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Total ({{ $booking->total_days }} hari)</span>
                        <span class="font-black text-[#0f2557]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3 border-t border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400 block">Peminjam</span>
                        <span class="font-bold text-slate-800">{{ $booking->borrower_name ?? $booking->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Kontak Peminjam</span>
                        <span class="font-bold text-slate-800">{{ $booking->borrower_phone ?? $booking->user->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Layanan</span>
                        <span class="font-bold text-slate-800">{{ $booking->with_driver ? 'Dengan Sopir (+Rp ' . number_format($booking->driver_fee, 0, ',', '.') . ')' : 'Lepas Kunci' }}</span>
                    </div>
                </div>
                @if ($booking->payment_deadline && $booking->booking_status === 'pending')
                    <p class="text-[11px] text-amber-700">Tenggat bayar: {{ $booking->payment_deadline->format('d M Y, H:i') }}</p>
                @endif
            </div>

            {{-- Form Pengembalian Mobil (Jika Ongoing) atau Ringkasan Pengembalian --}}
            @if ($booking->booking_status === 'ongoing')
                <div class="bg-white rounded-2xl border border-indigo-200 p-6 shadow-sm">
                    <h2 class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-4">Proses Pengembalian Mobil</h2>
                    <form action="{{ route('admin.rentals.return', $booking) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="actual_return_date" class="block text-xs font-bold text-slate-700 uppercase mb-1">Waktu Pengembalian Aktual *</label>
                                <input type="datetime-local" name="actual_return_date" id="actual_return_date" value="{{ now()->format('Y-m-d\TH:i') }}" required
                                    class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label for="late_fee_per_day" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tarif Telat / Hari (Rp) <span class="text-slate-400 normal-case font-normal">opsional</span></label>
                                <input type="number" name="late_fee_per_day" id="late_fee_per_day" min="0" placeholder="Kosongkan = pakai pengaturan sistem"
                                    class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                                <span class="text-[10px] text-slate-400">Denda otomatis dihitung jika melewati {{ $booking->end_date->format('d M Y H:i') }}</span>
                            </div>
                        </div>

                        <div>
                            <label for="damage_fee" class="block text-xs font-bold text-slate-700 uppercase mb-1">Denda Kerusakan (Rp)</label>
                            <input type="number" name="damage_fee" id="damage_fee" min="0" value="0"
                                class="w-full sm:w-1/2 px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                        </div>

                        <div>
                            <label for="condition_description" class="block text-xs font-bold text-slate-700 uppercase mb-1">Kondisi Mobil Saat Kembali *</label>
                            <textarea name="condition_description" id="condition_description" rows="3" required placeholder="Contoh: Mobil kembali bersih, bensin full, bodi mulus tanpa lecet."
                                class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm resize-none"></textarea>
                        </div>

                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm cursor-pointer">
                            Selesaikan Pengembalian
                        </button>
                    </form>
                </div>
            @elseif ($booking->carReturn)
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Catatan Pengembalian</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs mb-3">
                        <div>
                            <span class="text-slate-400 block">Status</span>
                            <span class="font-bold uppercase">{{ $booking->carReturn->return_status }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Kembali Aktual</span>
                            <span class="font-bold">{{ $booking->carReturn->actual_return_date ? $booking->carReturn->actual_return_date->format('d M Y H:i') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Denda Telat ({{ $booking->carReturn->late_days }} hari)</span>
                            <span class="font-bold {{ $booking->carReturn->late_fee > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                Rp {{ number_format($booking->carReturn->late_fee, 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Denda Kerusakan</span>
                            <span class="font-bold">Rp {{ number_format($booking->carReturn->damage_fee, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Total Denda</span>
                            <span class="font-black text-rose-600">Rp {{ number_format($booking->carReturn->total_fine, 0, ',', '.') }}</span>
                        </div>
                        @if ($booking->carReturn->hasFine())
                            <div>
                                <span class="text-slate-400 block">Status Denda</span>
                                <span class="font-bold uppercase">{{ $booking->carReturn->fine_status }}</span>
                                <a href="{{ route('admin.fines.index', ['status' => 'all']) }}" class="block text-blue-600 hover:underline">Kelola denda</a>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600">{{ $booking->carReturn->condition_description }}</p>
                </div>
            @endif
        </div>

        {{-- Panel Verifikasi Pembayaran --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm h-fit">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Pembayaran</h2>
            @if ($booking->payment)
                <div class="space-y-2.5 text-xs mb-4">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Metode</span>
                        <span class="font-bold">{{ $booking->payment->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nominal</span>
                        <span class="font-bold">Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="font-bold uppercase {{ $booking->payment->payment_status === 'verified' ? 'text-emerald-600' : ($booking->payment->payment_status === 'rejected' ? 'text-rose-600' : 'text-amber-600') }}">
                            {{ $booking->payment->payment_status }}
                        </span>
                    </div>
                </div>

                @if ($booking->payment->proof_image)
                    <a href="{{ asset('storage/' . $booking->payment->proof_image) }}" target="_blank" class="block mb-4">
                        <img src="{{ asset('storage/' . $booking->payment->proof_image) }}" alt="Bukti Bayar" class="w-full h-44 object-cover rounded-xl border border-slate-200">
                    </a>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 text-center text-xs text-slate-400 mb-4">Belum ada foto bukti transfer.</div>
                @endif

                @if ($booking->payment->payment_status === 'pending')
                    <div class="grid grid-cols-2 gap-2">
                        <form action="{{ route('admin.payments.verify', $booking->payment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl cursor-pointer">
                                Verify
                            </button>
                        </form>
                        <form action="{{ route('admin.payments.reject', $booking->payment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 cursor-pointer">
                                Reject
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <p class="text-xs text-slate-400">Belum ada catatan pembayaran.</p>
            @endif
        </div>
    </div>
@endsection
