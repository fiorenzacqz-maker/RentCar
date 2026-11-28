@extends('layouts.dashboard')

@section('title', 'Pembayaran Sewa')
@section('page-title', 'Pembayaran Sewa #' . $booking->id)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/70">
            <h2 class="text-lg font-bold text-[#0f2557]">Pembayaran Sewa Mobil #{{ $booking->id }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Silakan lakukan transfer sesuai nominal tagihan dan unggah bukti pembayaran.</p>
        </div>

        <div class="p-6 space-y-6">
            {{-- Info Rekening & Nominal --}}
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-sm text-slate-700 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-blue-800">
                    <i data-lucide="landmark" class="w-4 h-4"></i>
                    <span>Rekening Tujuan Transfer Manual</span>
                </div>
                <p class="whitespace-pre-line text-slate-900 font-semibold">{{ \App\Models\Setting::get('bank_info') }}</p>
                <div class="pt-2 mt-2 border-t border-blue-200 flex justify-between items-center">
                    <span class="font-medium text-slate-600">Total Tagihan:</span>
                    <span class="text-lg font-extrabold text-blue-700">Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer.payments.store', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-slate-700 mb-1.5">Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                        <option value="">-- Pilih Metode Pembayaran --</option>
                        <option value="Transfer Bank" {{ old('payment_method') === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="QRIS / E-Wallet" {{ old('payment_method') === 'QRIS / E-Wallet' ? 'selected' : '' }}>QRIS / E-Wallet</option>
                    </select>
                </div>

                {{-- Kotak Kode QRIS (Muncul Otomatis saat QRIS / E-Wallet dipilih) --}}
                <div id="qrisBox" class="hidden p-5 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                        <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                        <span>Scan QRIS RentCar Indonesia</span>
                    </div>
                    <div class="flex justify-center">
                        <img src="{{ \App\Models\Setting::qrisUrl() }}"
                             alt="QRIS Pembayaran RentCar"
                             class="w-60 max-w-full rounded-xl border border-slate-200 bg-white p-2 shadow-sm">
                    </div>
                    <p class="text-xs text-slate-500">
                        Scan kode QR di atas menggunakan aplikasi <strong>GoPay, OVO, DANA, ShopeePay,</strong> atau <strong>Mobile Banking</strong> sebesar
                        <strong class="text-slate-800">Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</strong>.
                    </p>
                </div>

                <div>
                    <label for="proof_image" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Unggah Bukti Pembayaran <span class="text-slate-400 font-normal">(JPG, PNG, WEBP, PDF — Maks. 1 GB)</span>
                    </label>
                    <input type="file" name="proof_image" id="proof_image" accept=".jpg,.jpeg,.png,.webp,.pdf" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors cursor-pointer">
                        Kirim Bukti Pembayaran
                    </button>
                    <a href="{{ route('customer.payments.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition-colors">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const paymentSelect = document.getElementById('payment_method');
        const qrisBox = document.getElementById('qrisBox');

        function toggleQris() {
            if (paymentSelect.value === 'QRIS / E-Wallet') {
                qrisBox.classList.remove('hidden');
            } else {
                qrisBox.classList.add('hidden');
            }
        }

        paymentSelect.addEventListener('change', toggleQris);
        toggleQris();
    });
</script>
@endpush