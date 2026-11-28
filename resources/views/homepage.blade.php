{{--
    Halaman Utama (Customer) - Sistem Manajemen Rental Mobil
    ========================================================
    Sesuai acuan desain visual pada referensi UI/UX & Terhubung ke Database MySQL/SQLite:
    - Navbar modern dengan navigasi jelas
    - Hero section modern dengan form pencarian
    - Katalog mobil pilihan dengan card yang rapi & konsisten dari database
    - Desain responsif (desktop, tablet & mobile)
    - Tema warna navy/dark blue (#0f2557), putih, dan aksen biru muda
    - Tidak menyertakan proses booking/pembayaran (hanya tampilan homepage & katalog)
--}}

@extends('layouts.app')

@section('title', 'RentCar - Rental Mobil Mudah & Nyaman')

@section('content')

{{-- =========================================================================
     1. HERO SECTION (Sesuai Mockup Desain: Banner Modern Bernuansa Cerah & Rapi)
     ========================================================================= --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-12">
    <div class="relative bg-gradient-to-r from-[#e9f0f8] via-[#e2ecf7] to-[#d8e6f5] rounded-3xl overflow-hidden border border-slate-200/60 shadow-sm">
        {{-- Siluet Latar Belakang Gedung Perkotaan Halus --}}
        <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#1e40af_1px,transparent_1px)] [background-size:16px_16px]"></div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center min-h-[460px] p-8 sm:p-12 lg:p-14 relative z-10">
            {{-- Kolom Kiri: Headline & Widget Pencarian --}}
            <div class="lg:col-span-7 space-y-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#0f2557] tracking-tight leading-[1.15]">
                        Rental Mobil<br>
                        <span class="text-blue-600">Mudah & Nyaman</span>
                    </h1>
                    <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-xl leading-relaxed">
                        Temukan mobil terbaik untuk perjalananmu dengan harga terjangkau.
                    </p>
                </div>

                {{-- Widget Filter & Pencarian Sederhana --}}
                <div class="bg-white/90 backdrop-blur-md p-2.5 sm:p-3 rounded-2xl sm:rounded-full shadow-lg border border-white/80 max-w-xl">
                    <form onsubmit="event.preventDefault(); triggerSearch();" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        {{-- Input Pencarian Nama / Merek Mobil --}}
                        <div class="flex-1 flex items-center gap-2.5 px-3 py-2 sm:py-1">
                            <i data-lucide="search" class="w-5 h-5 text-slate-400 shrink-0"></i>
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Cari nama atau merek mobil..."
                                class="w-full bg-transparent border-none text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-0 font-medium"
                                oninput="filterCars()"
                            >
                        </div>

                        {{-- Dropdown Kategori Cepat --}}
                        <div class="border-t sm:border-t-0 sm:border-l border-slate-200 px-3 py-2 sm:py-1 flex items-center gap-2">
                            <i data-lucide="sliders-horizontal" class="w-4 h-4 text-slate-400 shrink-0"></i>
                            <select
                                id="categorySelect"
                                onchange="filterCars()"
                                class="bg-transparent border-none text-xs sm:text-sm text-slate-700 font-medium focus:outline-none focus:ring-0 cursor-pointer pr-4"
                            >
                                <option value="all">Semua Tipe</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tombol Cari Mobil --}}
                        <button
                            type="button"
                            onclick="triggerSearch()"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl sm:rounded-full font-semibold text-sm flex items-center justify-center gap-2 shadow-md hover:shadow transition-all shrink-0 cursor-pointer"
                        >
                            <i data-lucide="search" class="w-4 h-4"></i>
                            <span>Cari Mobil</span>
                        </button>
                    </form>
                </div>

                {{-- Fitur Singkat di Bawah Form --}}
                <div class="flex flex-wrap items-center gap-6 pt-2 text-xs font-semibold text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-600"></i>
                        <span>Harga Transparan</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-600"></i>
                        <span>Kondisi Mobil Prima</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-600"></i>
                        <span>Bisa Lepas Kunci</span>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Foto Mobil Utama Hero (Sesuai Referensi Sedan Putih Mewah) --}}
            <div class="lg:col-span-5 flex justify-center lg:justify-end relative">
                <div class="relative w-full max-w-lg">
                    {{-- Lingkaran Dekorasi di Belakang Mobil --}}
                    <div class="absolute -top-10 -right-10 w-72 h-72 bg-blue-300/30 rounded-full blur-2xl pointer-events-none"></div>

                    {{-- Gambar Mobil Sedan Modern --}}
                    <img
                        src="https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=800&q=80"
                        alt="RentCar - Mobil Rental Nyaman"
                        class="w-full h-auto object-contain rounded-2xl drop-shadow-2xl relative z-10 transition-transform duration-500 hover:scale-[1.02]"
                    >
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================================
     2. KATALOG MOBIL ("Mobil Pilihan")
     ========================================================================= --}}
<section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 scroll-mt-24">
    {{-- Header Bagian Katalog Sesuai Desain Mockup --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8">
        <div>
            {{-- Indikator Garis Biru di Atas Judul (Sesuai Referensi Mockup) --}}
            <div class="w-12 h-1 bg-blue-600 rounded-full mb-2"></div>
            <h2 class="text-2xl sm:text-3xl font-black text-[#0f2557] tracking-tight">
                Mobil Pilihan
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Koleksi kendaraan terbaik, terawat, dan siap untuk disewa.
            </p>
        </div>

        {{-- Filter Kategori Berupa Tab / Pill --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 sm:pb-0 w-full sm:w-auto" id="filterTabs">
            <button
                type="button"
                onclick="setCategoryFilter('all', this)"
                class="category-tab px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-600 text-white shadow-sm transition-all cursor-pointer"
                data-category="all"
            >
                Semua
            </button>
            @foreach($categories as $cat)
                <button
                    type="button"
                    onclick="setCategoryFilter('{{ $cat->name }}', this)"
                    class="category-tab px-4 py-1.5 rounded-full text-xs font-semibold bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 transition-all cursor-pointer"
                    data-category="{{ $cat->name }}"
                >
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Grid Kartu Mobil --}}
    {{-- Data mobil diambil dari database (Tabel cars & car_categories) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="carCardsGrid">
        @forelse ($cars as $car)
            <div
                class="car-card-item"
                data-name="{{ strtolower($car->name) }}"
                data-brand="{{ strtolower($car->brand) }}"
                data-category="{{ $car->category->name ?? '' }}"
            >
                @include('components.car-card', ['car' => $car])
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 p-8">
                <i data-lucide="car-front" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <h3 class="font-bold text-slate-700">Belum ada data mobil</h3>
                <p class="text-sm text-slate-500 mt-1">Jalankan migration dan seeder database untuk mengisi data mobil.</p>
            </div>
        @endforelse
    </div>

    {{-- Pesan Ketika Hasil Pencarian / Filter Kosong --}}
    <div id="noResultsMessage" class="hidden py-16 text-center bg-white rounded-2xl border border-slate-200 p-8 my-6">
        <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="search-x" class="w-8 h-8"></i>
        </div>
        <h3 class="font-bold text-lg text-slate-800">Mobil Tidak Ditemukan</h3>
        <p class="text-sm text-slate-500 max-w-md mx-auto mt-1 mb-5">
            Tidak ada mobil yang cocok dengan kriteria pencarian Anda. Coba kata kunci atau jenis mobil lain.
        </p>
        <button
            type="button"
            onclick="resetFilters()"
            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-colors cursor-pointer"
        >
            Reset Pencarian
        </button>
    </div>
</section>

{{-- =========================================================================
     3. BAGIAN KEUNGGULAN (Mengapa Memilih Kami?)
     ========================================================================= --}}
<section id="keunggulan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 scroll-mt-20">
    <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
            Keunggulan Kami
        </span>
        <h2 class="text-2xl sm:text-3xl font-black text-[#0f2557] tracking-tight mt-3">
            Mengapa Memilih RentCar?
        </h2>
        <p class="text-sm text-slate-500 mt-2">
            Kami menghadirkan pengalaman rental mobil yang aman, nyaman, dan transparan untuk setiap perjalanan Anda.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Keunggulan 1: Pilihan Mobil Beragam --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                <i data-lucide="car" class="w-6 h-6"></i>
            </div>
            <h3 class="font-bold text-base text-slate-900 mb-2">Pilihan Mobil Beragam</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Tersedia berbagai pilihan mulai dari city car hemat, MPV keluarga, hingga SUV premium yang siap jalan.
            </p>
        </div>

        {{-- Keunggulan 2: Harga Transparan --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                <i data-lucide="badge-dollar-sign" class="w-6 h-6"></i>
            </div>
            <h3 class="font-bold text-base text-slate-900 mb-2">Harga Transparan</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Tarif jelas tanpa biaya tersembunyi. Apa yang Anda lihat pada katalog adalah harga yang Anda bayarkan.
            </p>
        </div>

        {{-- Keunggulan 3: Proses Cepat & Mudah --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </div>
            <h3 class="font-bold text-base text-slate-900 mb-2">Proses Cepat & Mudah</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Persyaratan sederhana dengan verifikasi cepat sehingga mobil dapat segera Anda gunakan tepat waktu.
            </p>
        </div>

        {{-- Keunggulan 4: Armada Terawat & Bersih --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3 class="font-bold text-base text-slate-900 mb-2">Armada Terawat & Aman</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Setiap unit rutin diservis berkala, dibersihkan higienis, dan diasuransikan demi keselamatan perjalanan.
            </p>
        </div>
    </div>
</section>

{{-- =========================================================================
     4. BAGIAN TENTANG KAMI
     ========================================================================= --}}
<section id="tentang" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 scroll-mt-20">
    <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 lg:p-14 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            {{-- Kolom Kiri: Foto / Ilustrasi Armada --}}
            <div class="lg:col-span-5 relative">
                <div class="rounded-2xl overflow-hidden shadow-md">
                    <img
                        src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80"
                        alt="Armada RentCar"
                        class="w-full h-80 object-cover"
                    >
                </div>
                {{-- Floating Badge Statistik --}}
                <div class="absolute -bottom-4 -right-4 bg-[#0f2557] text-white p-4 rounded-2xl shadow-xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="award" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-lg font-black">5+ Tahun</div>
                        <div class="text-xs text-blue-200">Melayani Perjalanan</div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Deskripsi Tentang Kami --}}
            <div class="lg:col-span-7 space-y-5">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                    Tentang Kami
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-[#0f2557] tracking-tight">
                    Mitra Terpercaya Perjalanan Anda Sejak Hari Pertama
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    RentCar adalah penyedia jasa rental mobil profesional yang berkomitmen memberikan kenyamanan, keamanan, dan efisiensi transportasi bagi perorangan maupun perusahaan. Kami memahami bahwa mobilitas Anda adalah hal utama.
                </p>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Dengan komitmen pelayanan prima, setiap unit kami menjalani inspeksi kelayakan berkala serta pembersihan menyeluruh sebelum diserahkan kepada pelanggan. Kepuasan Anda adalah prioritas nomor satu kami.
                </p>

                {{-- 3 Indikator Statistik --}}
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-[#0f2557]">500+</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Pelanggan Puas</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-[#0f2557]">50+</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Armada Mobil</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-[#0f2557]">4.9★</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Rating Layanan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================================
     5. BAGIAN KONTAK / CALL TO ACTION (CTA)
     ========================================================================= --}}
<section id="kontak" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 scroll-mt-20">
    <div class="bg-gradient-to-r from-[#0f2557] via-[#152544] to-[#1e3a8a] rounded-3xl p-8 sm:p-12 lg:p-14 text-white shadow-xl relative overflow-hidden">
        {{-- Latar Belakang Lingkaran Cahaya --}}
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto text-center space-y-6">
            <h2 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                Siap Melakukan Perjalanan Bersama Kami?
            </h2>
            <p class="text-sm sm:text-base text-blue-200 leading-relaxed max-w-2xl mx-auto">
                Temukan mobil impian Anda sekarang juga atau hubungi tim customer support kami yang siap membantu menjawab pertanyaan Anda kapan saja.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                {{-- Tombol Lihat Katalog --}}
                <a
                    href="#katalog"
                    class="w-full sm:w-auto px-8 py-3.5 bg-white text-[#0f2557] hover:bg-slate-100 font-bold text-sm rounded-full shadow-lg hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i data-lucide="car" class="w-4 h-4 text-blue-600"></i>
                    <span>Lihat Daftar Mobil</span>
                </a>

                {{-- Tombol Kontak Telepon / WhatsApp --}}
                <a
                    href="https://wa.me/6281234567890"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full sm:w-auto px-8 py-3.5 bg-blue-600/80 hover:bg-blue-600 text-white font-bold text-sm rounded-full border border-blue-400/40 shadow-lg hover:shadow transition-all flex items-center justify-center gap-2"
                >
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Hubungi CS (WhatsApp)</span>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================================
     MODAL POPUP DETAIL MOBIL
     ========================================================================= --}}
<div id="carDetailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100 transform transition-all">
        {{-- Header Modal --}}
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <div>
                <span id="modalCategory" class="text-xs font-bold text-blue-600 uppercase tracking-widest bg-blue-100/60 px-2.5 py-0.5 rounded-full"></span>
                <h3 id="modalTitle" class="text-xl font-bold text-slate-900 mt-1"></h3>
            </div>
            <button
                type="button"
                onclick="closeCarDetailModal()"
                class="w-8 h-8 rounded-full bg-slate-200/80 text-slate-600 hover:bg-slate-300 flex items-center justify-center transition-colors cursor-pointer"
                aria-label="Tutup Detail"
            >
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Isi Modal --}}
        <div class="p-6 space-y-4">
            <div class="h-48 bg-slate-50 rounded-2xl flex items-center justify-center p-4">
                <img id="modalImage" src="" alt="Mobil" class="max-h-40 w-auto object-contain">
            </div>
            <div id="modalGallery" class="hidden flex items-center gap-2 overflow-x-auto pb-1"></div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-2">
                <div class="p-3 bg-slate-50 rounded-xl text-center">
                    <span class="text-slate-400 block mb-0.5">Kapasitas</span>
                    <span id="modalSeats" class="font-bold text-slate-800"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl text-center">
                    <span class="text-slate-400 block mb-0.5">Transmisi</span>
                    <span id="modalTransmission" class="font-bold text-slate-800"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl text-center">
                    <span class="text-slate-400 block mb-0.5">Tahun</span>
                    <span id="modalYear" class="font-bold text-slate-800"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl text-center">
                    <span class="text-slate-400 block mb-0.5">No. Plat</span>
                    <span id="modalPlate" class="font-bold text-slate-800 font-mono"></span>
                </div>
            </div>

            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-100/80 flex justify-between items-center">
                <div>
                    <span class="text-xs text-slate-500">Tarif Sewa Harian</span>
                    <div id="modalPrice" class="text-xl font-black text-[#0f2557]"></div>
                </div>
                <span id="modalCarStatus" class="text-xs font-semibold px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full">Unit Tersedia</span>
            </div>

        </div>

        {{-- Footer Modal --}}
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-between gap-2">
            <button
                type="button"
                onclick="closeCarDetailModal()"
                class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition-colors cursor-pointer"
            >
                Tutup
            </button>
            @auth
                @if (in_array(auth()->user()->role, ['customer', 'admin', 'staff'], true))
                    <a id="modalRentalButton" data-rental-url="{{ route(auth()->user()->isStaffOrAdmin() ? 'admin.rentals.create' : 'customer.rentals.create', ['car_id' => '__CAR_ID__']) }}" href="#" class="hidden px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors items-center justify-center gap-2">
                        <i data-lucide="car-front" class="w-4 h-4"></i>
                        Rental Mobil Ini
                    </a>
                @endif
            @else
                <a id="modalRentalButton" href="{{ route('login') }}" class="hidden px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors items-center justify-center gap-2">
                    <i data-lucide="car-front" class="w-4 h-4"></i>
                    Login untuk Rental
                </a>
            @endauth
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // State Filter Aktif
    let currentCategory = 'all';

    // Filter Mobil Berdasarkan Input Text & Kategori
    function filterCars() {
        const query = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
        const selectCategory = document.getElementById('categorySelect')?.value || 'all';

        // Sinkronisasi kategori aktif dari tab atau select
        const activeCategory = (selectCategory !== 'all') ? selectCategory : currentCategory;

        const cards = document.querySelectorAll('.car-card-item');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const brand = card.dataset.brand || '';
            const category = card.dataset.category || '';

            const matchesQuery = query === '' || name.includes(query) || brand.includes(query);
            const matchesCategory = activeCategory === 'all' || category === activeCategory;

            if (matchesQuery && matchesCategory) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Tampilkan pesan kosong jika tidak ada hasil
        const noResults = document.getElementById('noResultsMessage');
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    // Set Kategori dari Tab Pill
    function setCategoryFilter(category, clickedButton) {
        currentCategory = category;

        // Update tampilan tab
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
            tab.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');
        });

        if (clickedButton) {
            clickedButton.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200');
            clickedButton.classList.add('bg-blue-600', 'text-white', 'shadow-sm');
        }

        // Sinkronkan ke dropdown jika ada
        const select = document.getElementById('categorySelect');
        if (select) {
            select.value = category;
        }

        filterCars();
    }

    // Trigger Tombol Cari Mobil (Scroll ke katalog & terapkan filter)
    function triggerSearch() {
        filterCars();
        const katalogSection = document.getElementById('katalog');
        if (katalogSection) {
            katalogSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Reset Semua Filter
    function resetFilters() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';

        const select = document.getElementById('categorySelect');
        if (select) select.value = 'all';

        setCategoryFilter('all', document.querySelector('.category-tab[data-category="all"]'));
    }

    // Modal Detail Mobil (Tampilan Saja + Galeri Foto)
    function showCarDetailModal(name, brand, price, seats, transmission, category, image, year, plate, carId, carStatus, triggerBtn) {
        document.getElementById('modalTitle').textContent = name;
        document.getElementById('modalCategory').textContent = category;
        document.getElementById('modalPrice').textContent = price;
        document.getElementById('modalSeats').textContent = seats;
        document.getElementById('modalTransmission').textContent = transmission;
        document.getElementById('modalImage').src = image;
        document.getElementById('modalYear').textContent = year || '-';
        document.getElementById('modalPlate').textContent = plate || '-';

        // Render thumbnail galeri foto tambahan jika ada
        const galleryContainer = document.getElementById('modalGallery');
        if (galleryContainer) {
            galleryContainer.innerHTML = '';
            let galleryUrls = [];
            try {
                galleryUrls = triggerBtn && triggerBtn.dataset.gallery ? JSON.parse(triggerBtn.dataset.gallery) : [];
            } catch (e) {
                galleryUrls = [];
            }

            if (galleryUrls.length > 1) {
                galleryContainer.classList.remove('hidden');
                galleryUrls.forEach((url, idx) => {
                    const thumb = document.createElement('button');
                    thumb.type = 'button';
                    thumb.className = `shrink-0 w-16 h-12 rounded-xl border-2 overflow-hidden bg-slate-50 p-1 cursor-pointer transition-all ${idx === 0 ? 'border-blue-600' : 'border-slate-200 hover:border-blue-400'}`;
                    thumb.innerHTML = `<img src="${url}" alt="Foto ${idx + 1}" class="w-full h-full object-contain">`;
                    thumb.onclick = () => {
                        document.getElementById('modalImage').src = url;
                        galleryContainer.querySelectorAll('button').forEach(b => {
                            b.classList.remove('border-blue-600');
                            b.classList.add('border-slate-200');
                        });
                        thumb.classList.remove('border-slate-200');
                        thumb.classList.add('border-blue-600');
                    };
                    galleryContainer.appendChild(thumb);
                });
            } else {
                galleryContainer.classList.add('hidden');
            }
        }

        const statusBadge = document.getElementById('modalCarStatus');
        const rentalButton = document.getElementById('modalRentalButton');
        const isAvailable = carStatus === 'available';
        statusBadge.textContent = isAvailable ? 'Unit Tersedia' : (carStatus === 'maintenance' ? 'Dalam Perawatan' : 'Tidak Tersedia');
        statusBadge.className = isAvailable
            ? 'text-xs font-semibold px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full'
            : 'text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full';

        if (rentalButton) {
            rentalButton.classList.toggle('hidden', !isAvailable);
            rentalButton.style.display = isAvailable ? 'inline-flex' : 'none';
            if (isAvailable && rentalButton.dataset.rentalUrl) {
                rentalButton.href = rentalButton.dataset.rentalUrl.replace('__CAR_ID__', carId);
            }
        }

        document.getElementById('carDetailModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeCarDetailModal() {
        document.getElementById('carDetailModal').classList.add('hidden');
    }

    // Tutup modal jika klik di luar area modal
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('carDetailModal');
        if (e.target === modal) {
            closeCarDetailModal();
        }
    });
</script>
@endpush
