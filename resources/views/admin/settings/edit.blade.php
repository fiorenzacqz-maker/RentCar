@extends('layouts.dashboard')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Sistem</h1>
        <p class="mt-1 text-sm text-slate-500">Atur tenggat pembayaran, tarif sopir, tarif denda, serta info rekening & QRIS.</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs max-w-3xl">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Pembayaran & Layanan</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tenggat Bayar (menit) *</label>
                    <input type="number" name="payment_deadline_minutes" min="5" max="10080" required value="{{ old('payment_deadline_minutes', $settings['payment_deadline_minutes']) }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                    <span class="text-[10px] text-slate-400">Booking tanpa bukti bayar dibatalkan otomatis setelah waktu ini.</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tarif Jasa Sopir per Hari (Rp) *</label>
                    <input type="number" name="driver_fee_per_day" min="0" step="1000" required value="{{ old('driver_fee_per_day', (int) $settings['driver_fee_per_day']) }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Denda Keterlambatan</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Dasar Perhitungan *</label>
                    <select name="late_fee_mode" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                        <option value="hour" {{ old('late_fee_mode', $settings['late_fee_mode']) === 'hour' ? 'selected' : '' }}>Per Jam</option>
                        <option value="day" {{ old('late_fee_mode', $settings['late_fee_mode']) === 'day' ? 'selected' : '' }}>Per Hari</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tarif per Jam (Rp) *</label>
                    <input type="number" name="late_fee_per_hour" min="0" step="1000" required value="{{ old('late_fee_per_hour', (int) $settings['late_fee_per_hour']) }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tarif per Hari (Rp) *</label>
                    <input type="number" name="late_fee_per_day" min="0" step="1000" required value="{{ old('late_fee_per_day', (int) $settings['late_fee_per_day']) }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>
            <p class="text-[11px] text-slate-400">Keterlambatan dibulatkan ke atas (jam atau hari). Petugas masih bisa mengisi tarif per hari manual saat pemeriksaan pengembalian.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Info Rekening & QRIS</h2>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Info Rekening (satu rekening per baris)</label>
                <textarea name="bank_info" rows="4" maxlength="1000" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">{{ old('bank_info', $settings['bank_info']) }}</textarea>
            </div>
            <div class="flex items-center gap-4">
                <img src="{{ $settings['qris_url'] }}" alt="QRIS saat ini" class="w-24 h-24 object-contain bg-slate-50 rounded-xl border border-slate-200">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Gambar QRIS</label>
                    <input type="file" name="qris_image" accept="image/*" class="text-xs">
                </div>
            </div>
        </div>

        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl cursor-pointer">Simpan Pengaturan</button>
    </form>
@endsection
