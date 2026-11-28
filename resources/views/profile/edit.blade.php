@extends('layouts.dashboard')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Akun</h1>
        <p class="mt-1 text-sm text-slate-500">Perbarui informasi kontak, alamat, dan keamanan password akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Card Edit Profil --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i data-lucide="user-cog" class="w-4 h-4 text-blue-600"></i>
                Informasi Profil
            </h2>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3.5 py-2.5 border @error('name') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                        class="w-full px-3.5 py-2.5 border border-slate-200 bg-slate-50 text-slate-500 rounded-xl text-sm cursor-not-allowed">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required
                        class="w-full px-3.5 py-2.5 border @error('phone') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full px-3.5 py-2.5 border @error('address') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 resize-none">{{ old('address', $user->address) }}</textarea>
                    @error('address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all cursor-pointer">
                    Simpan Perubahan Profil
                </button>
            </form>
        </div>

        {{-- Card Ganti Password --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i data-lucide="key-round" class="w-4 h-4 text-amber-600"></i>
                Ganti Password
            </h2>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="w-full px-3.5 py-2.5 border @error('current_password') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    @error('current_password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password Baru</label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                        class="w-full px-3.5 py-2.5 border @error('password') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ulangi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-[#0f2557] hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-sm transition-all cursor-pointer">
                    Perbarui Password
                </button>
            </form>
        </div>
    </div>
@endsection
