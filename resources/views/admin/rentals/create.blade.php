@extends('layouts.dashboard')

@section('title', 'Buat Booking (Admin)')
@section('page-title', 'Buat Booking')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.rentals.index') }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Daftar Booking
        </a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Buat Booking Atas Nama Customer</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm max-w-2xl">
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.rentals.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="user_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">Customer *</label>
                <select name="user_id" id="user_id" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                    <option value="">-- Pilih Customer --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('user_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="car_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">Mobil *</label>
                <select name="car_id" id="car_id" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                    <option value="">-- Pilih Mobil --</option>
                    @foreach ($cars as $car)
                        <option value="{{ $car->id }}" {{ old('car_id', $selectedCarId) == $car->id ? 'selected' : '' }}>
                            {{ $car->brand }} {{ $car->name }} ({{ $car->plate_number }}) - Rp {{ number_format($car->price_per_day, 0, ',', '.') }}/hari
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="borrower_name" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Peminjam *</label>
                    <input type="text" name="borrower_name" id="borrower_name" maxlength="100" required value="{{ old('borrower_name') }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label for="borrower_phone" class="block text-xs font-bold text-slate-700 uppercase mb-1">Kontak Peminjam *</label>
                    <input type="text" name="borrower_phone" id="borrower_phone" maxlength="20" required value="{{ old('borrower_phone') }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal & Jam Pinjam *</label>
                    <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', now()->addHour()->minute(0)->format('Y-m-d\TH:i')) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal & Jam Kembali *</label>
                    <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', now()->addDay()->addHour()->minute(0)->format('Y-m-d\TH:i')) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="with_driver" value="1" {{ old('with_driver') ? 'checked' : '' }}>
                Dengan Sopir <span class="text-xs text-slate-400">(+Rp {{ number_format($driverFeePerDay, 0, ',', '.') }}/hari)</span>
            </label>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="payment_method" class="block text-xs font-bold text-slate-700 uppercase mb-1">Metode Pembayaran *</label>
                    <select name="payment_method" id="payment_method" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet</option>
                    </select>
                </div>
                <div>
                    <label for="payment_status" class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Pembayaran Awal *</label>
                    <select name="payment_status" id="payment_status" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                        <option value="verified">Langsung Lunas (Verified)</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan</label>
                <textarea name="notes" id="notes" rows="2" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-2 flex justify-end gap-3">
                <a href="{{ route('admin.rentals.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl cursor-pointer">Simpan Booking</button>
            </div>
        </form>
    </div>
@endsection
