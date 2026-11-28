@extends('layouts.dashboard')

@section('title', 'Form Sewa Mobil')
@section('page-title', 'Sewa Mobil')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Formulir Pemesanan Mobil</h1>
        <p class="mt-1 text-sm text-slate-500">Pilih unit mobil, tentukan tanggal & jam sewa, lalu pilih layanan. Sistem otomatis mengecek ketersediaan jadwal.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    <strong class="font-bold block mb-1">Periksa kembali data Anda:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer.rentals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="car_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Mobil *</label>
                    <select name="car_id" id="car_id" required onchange="updateEstimate()"
                        class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                        <option value="">-- Pilih Armada Mobil --</option>
                        @foreach ($cars as $car)
                            <option value="{{ $car->id }}"
                                data-price="{{ (float) $car->price_per_day }}"
                                data-name="{{ $car->brand }} {{ $car->name }}"
                                {{ (old('car_id', $selectedCar?->id) == $car->id) ? 'selected' : '' }}>
                                {{ $car->brand }} {{ $car->name }} ({{ $car->category->name ?? '-' }} &middot; {{ ucfirst($car->transmission) }}) - Rp {{ number_format($car->price_per_day, 0, ',', '.') }}/hari
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Data Peminjam --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="borrower_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Peminjam *</label>
                        <input type="text" name="borrower_name" id="borrower_name" maxlength="100" required value="{{ old('borrower_name', auth()->user()->name) }}"
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    </div>
                    <div>
                        <label for="borrower_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Kontak Peminjam *</label>
                        <input type="tel" name="borrower_phone" id="borrower_phone" maxlength="20" required value="{{ old('borrower_phone', auth()->user()->phone) }}" placeholder="08xxxxxxxxxx"
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    </div>
                </div>

                {{-- Tanggal & Jam --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal & Jam Pinjam *</label>
                        <input type="datetime-local" name="start_date" id="start_date" min="{{ now()->format('Y-m-d\TH:i') }}"
                            value="{{ old('start_date', now()->addHour()->minute(0)->format('Y-m-d\TH:i')) }}" required onchange="updateEstimate()"
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal & Jam Kembali *</label>
                        <input type="datetime-local" name="end_date" id="end_date" min="{{ now()->format('Y-m-d\TH:i') }}"
                            value="{{ old('end_date', now()->addDay()->addHour()->minute(0)->format('Y-m-d\TH:i')) }}" required onchange="updateEstimate()"
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 -mt-3">Durasi dihitung per 24 jam dan dibulatkan ke atas (minimal 1 hari).</p>

                {{-- Info Jadwal Terpesan untuk Mobil Terpilih --}}
                <div id="bookedDatesBox" class="hidden p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                    <strong class="font-bold block mb-1">Waktu sudah terpesan untuk mobil ini:</strong>
                    <ul id="bookedDatesList" class="list-disc list-inside space-y-0.5"></ul>
                </div>

                {{-- Layanan Sewa --}}
                <div>
                    <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Layanan Sewa *</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/60">
                            <input type="radio" name="service_type" value="self_drive" class="mt-0.5" onchange="updateEstimate()" {{ old('service_type', 'self_drive') === 'self_drive' ? 'checked' : '' }}>
                            <span>
                                <span class="block text-sm font-bold text-slate-800">Lepas Kunci</span>
                                <span class="block text-xs text-slate-500">Anda mengemudi sendiri. Tanpa biaya tambahan.</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/60">
                            <input type="radio" name="service_type" value="with_driver" class="mt-0.5" onchange="updateEstimate()" {{ old('service_type') === 'with_driver' ? 'checked' : '' }}>
                            <span>
                                <span class="block text-sm font-bold text-slate-800">Dengan Sopir (Driver)</span>
                                <span class="block text-xs text-slate-500">Ditemani sopir profesional. <strong class="text-blue-700">+Rp {{ number_format($driverFeePerDay, 0, ',', '.') }}/hari</strong></span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="payment_method" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Metode Pembayaran *</label>
                        <select name="payment_method" id="payment_method" required
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                            <option value="Transfer Bank" {{ old('payment_method') === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="E-Wallet" {{ old('payment_method') === 'E-Wallet' ? 'selected' : '' }}>E-Wallet (QRIS / GoPay / OVO)</option>
                            <option value="Tunai" {{ old('payment_method') === 'Tunai' ? 'selected' : '' }}>Tunai di Lokasi</option>
                        </select>
                    </div>

                    <div>
                        <label for="proof_image" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bukti Pembayaran <span class="text-slate-400 font-normal lowercase">(opsional, bisa menyusul)</span></label>
                        <input type="file" name="proof_image" id="proof_image" accept="image/*"
                            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea name="notes" id="notes" rows="2" maxlength="500" placeholder="Contoh: Titik penjemputan atau permintaan khusus."
                        class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 resize-none">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <a href="{{ route('customer.rentals.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">
                        Buat Pesanan Sekarang
                    </button>
                </div>
            </form>
        </div>

        {{-- Ringkasan Estimasi Biaya --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm h-fit">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Ringkasan Biaya</h2>
            <div class="space-y-3 text-sm border-b border-slate-100 pb-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Mobil</span>
                    <span id="summaryCar" class="font-bold text-slate-800 text-right">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Harga / Hari</span>
                    <span id="summaryPrice" class="font-semibold text-slate-700">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Durasi Sewa</span>
                    <span id="summaryDays" class="font-semibold text-slate-700">0 hari</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Subtotal Mobil</span>
                    <span id="summaryCarTotal" class="font-semibold text-slate-700">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Biaya Sopir</span>
                    <span id="summaryDriver" class="font-semibold text-slate-700">Rp 0</span>
                </div>
            </div>
            <div class="pt-4 flex justify-between items-center">
                <span class="text-xs font-bold uppercase text-slate-500">Estimasi Total</span>
                <span id="summaryTotal" class="text-xl font-black text-[#0f2557]">Rp 0</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Total final dihitung ulang oleh sistem saat pesanan dibuat.</p>

            <div class="mt-5 p-3.5 rounded-xl bg-blue-50/70 border border-blue-100 text-xs text-slate-600 space-y-1">
                <p class="font-bold text-blue-800">Info Rekening Transfer:</p>
                <p class="whitespace-pre-line">{{ $bankInfo }}</p>
                <p class="pt-1 text-slate-500">Batas pembayaran: <strong>{{ $deadlineMinutes >= 60 ? round($deadlineMinutes / 60, 1) . ' jam' : $deadlineMinutes . ' menit' }}</strong> setelah pesanan dibuat. Pesanan yang tidak dibayar akan dibatalkan otomatis.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const bookedDates = @json($bookedDates);
    const DRIVER_FEE_PER_DAY = {{ (float) $driverFeePerDay }};

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function updateEstimate() {
        const carSelect = document.getElementById('car_id');
        const option = carSelect.options[carSelect.selectedIndex];
        const carId = carSelect.value;
        const price = parseFloat(option?.dataset?.price || 0);
        const carName = option?.dataset?.name || '-';

        const startVal = document.getElementById('start_date').value;
        const endVal = document.getElementById('end_date').value;
        const withDriver = document.querySelector('input[name="service_type"]:checked')?.value === 'with_driver';

        // Durasi per 24 jam dibulatkan ke atas, minimal 1 hari (sama dengan aturan di server)
        let days = 0;
        if (startVal && endVal) {
            const diffMs = new Date(endVal) - new Date(startVal);
            days = diffMs > 0 ? Math.max(1, Math.ceil(diffMs / (1000 * 60 * 60 * 24))) : 0;
        }

        const carTotal = price * days;
        const driverTotal = withDriver ? DRIVER_FEE_PER_DAY * days : 0;

        document.getElementById('summaryCar').textContent = carName;
        document.getElementById('summaryPrice').textContent = formatRupiah(price);
        document.getElementById('summaryDays').textContent = days + ' hari';
        document.getElementById('summaryCarTotal').textContent = formatRupiah(carTotal);
        document.getElementById('summaryDriver').textContent = formatRupiah(driverTotal);
        document.getElementById('summaryTotal').textContent = formatRupiah(carTotal + driverTotal);

        const box = document.getElementById('bookedDatesBox');
        const list = document.getElementById('bookedDatesList');
        list.innerHTML = '';

        if (carId && bookedDates[carId] && bookedDates[carId].length > 0) {
            bookedDates[carId].forEach(item => {
                const li = document.createElement('li');
                li.textContent = `${item.start} s/d ${item.end}`;
                list.appendChild(li);
            });
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', updateEstimate);
</script>
@endpush
