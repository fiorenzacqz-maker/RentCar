@extends('layouts.dashboard')

@section('title', $user ? 'Edit Akun' : 'Tambah Akun')
@section('page-title', $user ? 'Edit Akun' : 'Tambah Akun')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">&larr; Kembali ke daftar akun</a>
        <h1 class="mt-1 text-2xl font-black text-slate-900 tracking-tight">{{ $user ? 'Edit Akun: ' . $user->name : 'Tambah Akun Baru' }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm max-w-xl">
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            @if ($user) @method('PUT') @endif

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama *</label>
                <input type="text" name="name" required maxlength="100" value="{{ old('name', $user->name ?? '') }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email *</label>
                <input type="email" name="email" required maxlength="100" value="{{ old('email', $user->email ?? '') }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Telepon</label>
                    <input type="text" name="phone" maxlength="20" value="{{ old('phone', $user->phone ?? '') }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Role *</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                        @foreach (['admin' => 'Admin', 'staff' => 'Petugas (Staff)', 'customer' => 'Customer'] as $value => $label)
                            <option value="{{ $value }}" {{ old('role', $user->role ?? 'customer') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat</label>
                <textarea name="address" rows="2" maxlength="500" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm resize-none">{{ old('address', $user->address ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password {{ $user ? '(kosongkan jika tidak diubah)' : '*' }}</label>
                    <input type="password" name="password" {{ $user ? '' : 'required' }} minlength="6" autocomplete="new-password" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
@endsection
