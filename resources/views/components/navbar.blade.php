{{-- Navbar - Navigasi utama website sesuai referensi desain --}}
<nav class="bg-white/95 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- Logo: Icon Mobil + RentCar --}}
            <a href="{{ route('homepage') }}" class="flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-200">
                    <i data-lucide="car" class="w-5 h-5"></i>
                </div>
                <span class="text-2xl font-extrabold text-[#0f2557] tracking-tight">Rent<span class="text-blue-600">Car</span></span>
            </a>

            {{-- Desktop Navigation Links --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('homepage') }}" class="text-blue-600 font-semibold relative py-1 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-blue-600 after:rounded-full">
                    Beranda
                </a>
                <a href="{{ route('homepage') }}#katalog" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">
                    Daftar Mobil
                </a>
                <a href="{{ route('homepage') }}#keunggulan" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">
                    Keunggulan
                </a>
                <a href="{{ route('homepage') }}#tentang" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">
                    Tentang Kami
                </a>
                <a href="{{ route('homepage') }}#kontak" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">
                    Kontak
                </a>
            </div>

            {{-- Desktop Actions: Search Icon, Login, Daftar / User Profile --}}
            <div class="hidden md:flex items-center gap-3">
                <a href="#katalog" title="Cari Mobil" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-full transition-colors">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </a>

                @auth
                    {{-- Profil Pengguna Masuk --}}
                    <div class="flex items-center gap-3 pl-2 border-l border-slate-200">
                        {{-- Tombol Dashboard --}}
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-full transition-colors flex items-center gap-1.5">
                            <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                            Dashboard
                        </a>

                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left leading-tight">
                                <span class="block text-xs font-bold text-slate-800 truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                                <span class="inline-block text-[10px] font-semibold uppercase px-1.5 py-0.2 rounded {{ Auth::user()->isAdmin() ? 'bg-amber-100 text-amber-800' : (Auth::user()->isStaff() ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ Auth::user()->role }}
                                </span>
                            </div>
                        </div>

                        {{-- Tombol Logout --}}
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button
                                type="submit"
                                title="Keluar Akun"
                                class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-full transition-colors cursor-pointer"
                            >
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Tombol Login: Outline Pill --}}
                    <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-semibold text-blue-600 border border-blue-600 rounded-full hover:bg-blue-50 transition-all">
                        Login
                    </a>
                    {{-- Tombol Daftar: Solid Blue Pill --}}
                    <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-sm hover:shadow transition-all">
                        Daftar
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Hamburger Button --}}
            <button
                type="button"
                id="mobileMenuBtn"
                onclick="toggleMobileMenu()"
                class="md:hidden p-2 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-slate-100 transition-colors focus:outline-none"
                aria-label="Toggle Navigation"
            >
                <i data-lucide="menu" id="menuOpenIcon" class="w-6 h-6"></i>
                <i data-lucide="x" id="menuCloseIcon" class="w-6 h-6 hidden"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Dropdown Menu --}}
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-slate-100 shadow-xl transition-all">
        <div class="px-5 py-4 space-y-3">
            <a href="{{ route('homepage') }}" class="block font-semibold text-blue-600 py-2 border-b border-slate-50">Beranda</a>
            <a href="{{ route('homepage') }}#katalog" class="block font-medium text-slate-600 hover:text-blue-600 py-2 border-b border-slate-50" onclick="closeMobileMenu()">Daftar Mobil</a>
            <a href="{{ route('homepage') }}#keunggulan" class="block font-medium text-slate-600 hover:text-blue-600 py-2 border-b border-slate-50" onclick="closeMobileMenu()">Keunggulan</a>
            <a href="{{ route('homepage') }}#tentang" class="block font-medium text-slate-600 hover:text-blue-600 py-2 border-b border-slate-50" onclick="closeMobileMenu()">Tentang Kami</a>
            <a href="{{ route('homepage') }}#kontak" class="block font-medium text-slate-600 hover:text-blue-600 py-2" onclick="closeMobileMenu()">Kontak</a>

            @auth
                <div class="pt-3 border-t border-slate-100 space-y-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold text-blue-600 py-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </a>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-slate-800">{{ Auth::user()->name }}</span>
                                <span class="inline-block text-[10px] font-semibold uppercase px-1.5 py-0.2 rounded {{ Auth::user()->isAdmin() ? 'bg-amber-100 text-amber-800' : (Auth::user()->isStaff() ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ Auth::user()->role }}
                                </span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-full transition-colors">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="pt-3 flex gap-3 border-t border-slate-100">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 text-sm font-semibold text-blue-600 border border-blue-600 rounded-full hover:bg-blue-50">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-sm">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const openIcon = document.getElementById('menuOpenIcon');
        const closeIcon = document.getElementById('menuCloseIcon');
        const isHidden = menu.classList.contains('hidden');

        if (isHidden) {
            menu.classList.remove('hidden');
            openIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
            openIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    }

    function closeMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const openIcon = document.getElementById('menuOpenIcon');
        const closeIcon = document.getElementById('menuCloseIcon');
        menu.classList.add('hidden');
        openIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    }
</script>