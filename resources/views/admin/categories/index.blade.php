@extends('layouts.dashboard')

@section('title', 'Kelola Kategori Mobil')
@section('page-title', 'Kategori Mobil')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kategori Mobil</h1>
        <p class="mt-1 text-sm text-slate-500">Tambah, edit, atau hapus kategori armada rental mobil.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Tambah Kategori --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm h-fit">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Tambah Kategori Baru</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Sedan"
                        class="w-full px-3.5 py-2.5 border @error('name') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat kategori..."
                        class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm resize-none">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm cursor-pointer">
                    Simpan Kategori
                </button>
            </form>
        </div>

        {{-- Daftar Kategori --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Kategori ({{ $categories->count() }})</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                            @csrf
                            @method('PUT')
                            <div>
                                <input type="text" name="name" value="{{ $category->name }}" required
                                    class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-bold text-slate-800">
                                <span class="text-[11px] text-slate-400 mt-1 block">{{ $category->cars_count }} unit mobil</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" name="description" value="{{ $category->description }}" placeholder="Deskripsi..."
                                    class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-600">
                                <button type="submit" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg shrink-0 cursor-pointer">
                                    Update
                                </button>
                            </div>
                        </form>

                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-400">Belum ada kategori mobil.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
