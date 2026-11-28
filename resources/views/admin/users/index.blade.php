@extends('layouts.dashboard')

@section('title', 'Manajemen Akun')
@section('page-title', 'Pengguna')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Akun</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola akun admin, petugas, dan customer.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm w-fit">+ Tambah Akun</a>
    </div>

    <form method="GET" class="mb-5 flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, email, telepon..." class="px-3.5 py-2 border border-slate-200 rounded-xl text-xs w-64">
        <select name="role" class="px-3 py-2 border border-slate-200 rounded-xl text-xs">
            <option value="">Semua Role</option>
            @foreach (['admin' => 'Admin', 'staff' => 'Petugas', 'customer' => 'Customer'] as $value => $label)
                <option value="{{ $value }}" {{ $role === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-xl cursor-pointer">Filter</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Email</th>
                    <th class="text-left px-4 py-3">Telepon</th>
                    <th class="text-left px-4 py-3">Role</th>
                    <th class="text-right px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $u)
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-800">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->phone ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full font-bold uppercase text-[10px] {{ $u->role === 'admin' ? 'bg-amber-100 text-amber-800' : ($u->role === 'staff' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">{{ $u->role }}</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 font-bold hover:underline mr-3">Edit</a>
                            @if ($u->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 font-bold hover:underline cursor-pointer">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">Tidak ada akun ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
