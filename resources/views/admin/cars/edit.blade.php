@extends('layouts.dashboard')

@section('title', 'Edit Mobil')
@section('page-title', 'Edit Mobil')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.cars.index') }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Daftar Mobil
        </a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit {{ $car->brand }} {{ $car->name }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm max-w-3xl">
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.cars.update', $car) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori *</label>
                    <select name="category_id" id="category_id" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $car->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase mb-1">Status *</label>
                    <select name="status" id="status" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                        <option value="available" {{ old('status', $car->status) === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ old('status', $car->status) === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        <option value="maintenance" {{ old('status', $car->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="brand" class="block text-xs font-bold text-slate-700 uppercase mb-1">Merek *</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $car->brand) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Mobil *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $car->name) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="plate_number" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Plat *</label>
                    <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number', $car->plate_number) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label for="year" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tahun *</label>
                    <input type="number" name="year" id="year" value="{{ old('year', $car->year) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label for="seats" class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah Kursi *</label>
                    <input type="number" name="seats" id="seats" value="{{ old('seats', $car->seats) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="transmission" class="block text-xs font-bold text-slate-700 uppercase mb-1">Transmisi *</label>
                    <select name="transmission" id="transmission" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                        <option value="automatic" {{ old('transmission', $car->transmission) === 'automatic' ? 'selected' : '' }}>Automatic</option>
                        <option value="manual" {{ old('transmission', $car->transmission) === 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div>
                    <label for="price_per_day" class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga per Hari (Rp) *</label>
                    <input type="number" name="price_per_day" id="price_per_day" value="{{ old('price_per_day', (int) $car->price_per_day) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm resize-none">{{ old('description', $car->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                <div>
                    <label for="image" class="block text-xs font-bold text-slate-700 uppercase mb-1">Ganti Foto Utama (Opsional)</label>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <span class="block text-xs font-bold text-slate-500 mb-1">Foto Saat Ini:</span>
                    <img src="{{ $car->image_url }}" alt="{{ $car->name }}" class="h-16 rounded-lg border border-slate-200 object-cover">
                </div>
            </div>

            <div>
                <label for="gallery" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tambah Foto Galeri (Opsional)</label>
                <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                @if ($car->images->isNotEmpty())
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($car->images as $img)
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Galeri" class="h-14 w-20 rounded-lg border border-slate-200 object-cover">
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pt-3 flex justify-end gap-3">
                <a href="{{ route('admin.cars.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl cursor-pointer">Perbarui Mobil</button>
            </div>
        </form>
    </div>
@endsection
