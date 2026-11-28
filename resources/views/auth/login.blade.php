<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - RentCar Rental Mobil</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            800: '#152544',
                            900: '#0f1f3d',
                            950: '#0a1428',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Lucide Icons CDN --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        {{-- Logo Brand --}}
        <a href="{{ route('homepage') }}" class="flex items-center justify-center gap-2.5 mb-2 group">
            <div class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                <i data-lucide="car" class="w-6 h-6"></i>
            </div>
            <span class="text-3xl font-black text-[#0f2557] tracking-tight">Rent<span class="text-blue-600">Car</span></span>
        </a>

        <h2 class="mt-4 text-center text-2xl font-black text-slate-900 tracking-tight">
            Masuk ke Akun Anda
        </h2>
        <p class="mt-1 text-center text-sm text-slate-500">
            Atau
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-500 hover:underline">
                daftar akun customer baru
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        {{-- Card Form Login --}}
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/60 rounded-3xl border border-slate-100">
            {{-- Alert Error Global --}}
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-700 text-sm flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5 text-rose-600"></i>
                    <div>
                        <strong class="font-semibold block mb-0.5">Terjadi kesalahan</strong>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Input Email --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="nama@email.com"
                            class="block w-full pl-10 pr-3.5 py-3 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium"
                        >
                    </div>
                </div>

                {{-- Input Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Password
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            placeholder="••••••••"
                            class="block w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium"
                        >
                        <button
                            type="button"
                            onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                        >
                            <i data-lucide="eye" id="eyeIcon" class="w-4 h-4"></i>
                            <i data-lucide="eye-off" id="eyeOffIcon" class="w-4 h-4 hidden"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                        >
                        <span class="text-xs font-medium text-slate-600">Ingat Saya di Perangkat Ini</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-xl transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk Sekarang</span>
                </button>
            </form>

            {{-- Demo Helper Buttons (Sangat Membantu Saat Presentasi / Demo Mahasiswa) --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center mb-3">
                    Akses Demo Cepat (Klik untuk Isi Otomatis)
                </p>
                <div class="grid grid-cols-2 gap-2.5">
                    <button
                        type="button"
                        onclick="fillCredentials('admin@rentcar.com', 'admin123')"
                        class="p-2.5 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl text-left transition-all group cursor-pointer"
                    >
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700 group-hover:text-blue-600">
                            <i data-lucide="shield" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Akun Admin</span>
                        </div>
                        <span class="text-[10px] text-slate-400 block mt-0.5">admin@rentcar.com</span>
                    </button>

                    <button
                        type="button"
                        onclick="fillCredentials('staff@rentcar.com', 'staff123')"
                        class="p-2.5 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl text-left transition-all group cursor-pointer"
                    >
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700 group-hover:text-blue-600">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Akun Petugas</span>
                        </div>
                        <span class="text-[10px] text-slate-400 block mt-0.5">staff@rentcar.com</span>
                    </button>

                    <button
                        type="button"
                        onclick="fillCredentials('kafka@gmail.com', 'password123')"
                        class="p-2.5 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl text-left transition-all group cursor-pointer"
                    >
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700 group-hover:text-blue-600">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Akun Customer 1</span>
                        </div>
                        <span class="text-[10px] text-slate-400 block mt-0.5">kafka@gmail.com</span>
                    </button>

                    <button
                        type="button"
                        onclick="fillCredentials('ahmad.rizky@gmail.com', 'password123')"
                        class="p-2.5 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl text-left transition-all group cursor-pointer"
                    >
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700 group-hover:text-blue-600">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Akun Customer 2</span>
                        </div>
                        <span class="text-[10px] text-slate-400 block mt-0.5">ahmad.rizky@gmail.com</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Kembali ke Beranda --}}
        <div class="mt-6 text-center">
            <a href="{{ route('homepage') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Halaman Utama</span>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const eye = document.getElementById('eyeIcon');
            const eyeOff = document.getElementById('eyeOffIcon');

            if (pwd.type === 'password') {
                pwd.type = 'text';
                eye.classList.add('hidden');
                eyeOff.classList.remove('hidden');
            } else {
                pwd.type = 'password';
                eye.classList.remove('hidden');
                eyeOff.classList.add('hidden');
            }
        }

        function fillCredentials(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
