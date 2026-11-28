{{--
    Kartu Mobil - Komponen Reusable Katalog Mobil
    Menggunakan kolom database resmi (cars & car_categories):
    - Foto mobil dari storage: asset('storage/' . $car->image)
    - Nama mobil & merek
    - Harga per hari (format Rupiah rapi)
    - Spesifikasi: Kursi & Transmisi
    - Tombol "Lihat Detail" (tampilan saja / non-booking)
--}}

@php
    $carName = $car->brand . ' ' . $car->name;
    $carCategory = $car->category->name ?? 'Mobil';
    $carSeats = $car->seats;
    $carTrans = ucfirst($car->transmission);
    $carPrice = $car->price_per_day;
    $carImgSrc = asset('storage/' . $car->image);
    $carStatus = $car->status === 'available' ? 'Tersedia' : ($car->status === 'maintenance' ? 'Perawatan' : 'Tidak Tersedia');
    $galleryUrls = collect([$carImgSrc])
        ->merge($car->images->map(fn ($img) => asset('storage/' . $img->image_path)))
        ->unique()
        ->values();
@endphp

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col group h-full relative">
    {{-- Aksen Garis Biru Halus di Atas Kartu (seperti pada referensi mockup) --}}
    <div class="h-1 bg-gradient-to-r from-blue-600 to-blue-400 opacity-0 group-hover:opacity-100 transition-opacity"></div>

    {{-- Area Foto Mobil --}}
    <div class="relative bg-gradient-to-b from-slate-50/70 to-white pt-6 pb-4 px-6 flex items-center justify-center h-48 overflow-hidden">
        <img
            src="{{ $carImgSrc }}"
            alt="{{ $carName }}"
            class="max-h-36 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
            loading="lazy"
            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=600&q=80';"
        >

        {{-- Badge Status Ketersediaan --}}
        <span class="absolute top-3 right-3 text-xs font-semibold px-2.5 py-1 rounded-full {{ $car->status === 'available' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-600' }}">
            {{ $carStatus }}
        </span>
    </div>

    {{-- Konten Informasi Mobil --}}
    <div class="p-5 flex flex-col flex-grow">
        {{-- Nama Mobil --}}
        <h3 class="font-bold text-lg text-slate-900 group-hover:text-blue-600 transition-colors">
            {{ $carName }}
        </h3>

        {{-- Harga Per Hari --}}
        <div class="mt-1.5 flex items-baseline gap-1">
            <span class="text-xl font-extrabold text-[#0f2557]">
                Rp {{ number_format($carPrice, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400 font-medium">/ hari</span>
        </div>

        {{-- Rating & Ulasan --}}
        @php $avgRating = $car->averageRating(); @endphp
        <a href="{{ route('cars.show', $car) }}" class="mt-2 inline-flex items-center gap-1 text-xs text-slate-500 hover:text-blue-600">
            @if ($avgRating)
                <span class="text-amber-500">&#9733;</span>
                <span class="font-bold text-slate-700">{{ number_format($avgRating, 1) }}</span>
                <span>({{ $car->reviews_count ?? $car->reviews()->count() }} ulasan)</span>
            @else
                <span class="text-slate-400">Belum ada ulasan</span>
            @endif
        </a>

        {{-- Spesifikasi / Fitur (Kursi & Transmisi) --}}
        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs font-medium text-slate-500">
            <div class="flex items-center gap-1.5">
                <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                <span>{{ $carSeats }} Kursi</span>
            </div>
            <div class="flex items-center gap-1.5">
                <i data-lucide="gauge" class="w-4 h-4 text-blue-600"></i>
                <span>{{ $carTrans }}</span>
            </div>
        </div>

        {{-- Tombol Detail dan Rental --}}
        <div class="mt-5 pt-1 grid grid-cols-2 gap-2">
            <button
                type="button"
                data-gallery='@json($galleryUrls)'
                onclick="showCarDetailModal('{{ addslashes($carName) }}', '{{ addslashes($car->brand) }}', 'Rp {{ number_format($carPrice, 0, ',', '.') }} / hari', '{{ $carSeats }} Kursi', '{{ $carTrans }}', '{{ addslashes($carCategory) }}', '{{ $carImgSrc }}', '{{ $car->year }}', '{{ $car->plate_number }}', '{{ $car->id }}', '{{ $car->status }}', this)"
                class="w-full py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-all duration-150 flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Lihat Detail</span>
            </button>

            @auth
                @if (in_array(auth()->user()->role, ['customer', 'admin', 'staff'], true) && $car->status === 'available')
                    <a href="{{ route(auth()->user()->isStaffOrAdmin() ? 'admin.rentals.create' : 'customer.rentals.create', ['car_id' => $car->id]) }}"
                       class="w-full py-2.5 px-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm rounded-xl shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-2">
                        <i data-lucide="car-front" class="w-4 h-4"></i>
                        <span>Rental</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="w-full py-2.5 px-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-all duration-150 flex items-center justify-center gap-2">
                    <i data-lucide="car-front" class="w-4 h-4"></i>
                    <span>Rental</span>
                </a>
            @endauth
        </div>
    </div>
</div>
