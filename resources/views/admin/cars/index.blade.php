@extends('layouts.dashboard')

@section('title', 'Kelola Mobil')
@section('page-title', 'Kelola Mobil')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Armada Mobil</h1>
            <p class="mt-1 text-sm text-slate-500">Tambah, perbarui spesifikasi, harga sewa, atau status ketersediaan mobil.</p>
        </div>
        <a href="{{ route('admin.cars.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Mobil</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-3.5">Mobil</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Plat & Tahun</th>
                        <th class="px-6 py-3.5">Harga / Hari</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($cars as $car)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $car->image_url }}" alt="{{ $car->name }}" class="w-14 h-10 rounded-lg object-cover bg-slate-100 shrink-0">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $car->brand }} {{ $car->name }}</p>
                                        <p class="text-xs text-slate-400">{{ ucfirst($car->transmission) }} &middot; {{ $car->seats }} kursi</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-600">{{ $car->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <span class="font-mono font-bold block">{{ $car->plate_number }}</span>
                                <span class="text-slate-400">{{ $car->year }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-black text-[#0f2557]">Rp {{ number_format($car->price_per_day, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full
                                    {{ $car->status === 'available' ? 'bg-emerald-100 text-emerald-800' : ($car->status === 'maintenance' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $car->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('admin.cars.edit', $car) }}" class="inline-block px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg">
                                    Edit
                                </a>
                                <form action="{{ route('admin.cars.destroy', $car) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mobil {{ $car->brand }} {{ $car->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">Belum ada data mobil.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $cars->links() }}
        </div>
    </div>
@endsection
