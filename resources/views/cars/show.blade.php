@extends('layouts.app')

@section('title', $car->brand . ' ' . $car->name)

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <a href="{{ route('homepage') }}#katalog" class="text-xs font-semibold text-blue-600 hover:underline">&larr; Kembali ke katalog</a>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 flex items-center justify-center">
                <img src="{{ $car->image_url }}" alt="{{ $car->brand }} {{ $car->name }}" class="max-h-64 w-auto object-contain">
            </div>

            <div>
                <span class="text-xs font-bold text-blue-600 uppercase">{{ $car->category->name ?? 'Mobil' }}</span>
                <h1 class="text-3xl font-black text-slate-900">{{ $car->brand }} {{ $car->name }}</h1>
                <p class="mt-1 text-2xl font-extrabold text-[#0f2557]">Rp {{ number_format($car->price_per_day, 0, ',', '.') }} <span class="text-sm font-medium text-slate-400">/ hari</span></p>

                @php $avg = $car->averageRating(); @endphp
                <p class="mt-2 text-sm text-slate-600">
                    @if ($avg)
                        <span class="text-amber-500">&#9733;</span> <strong>{{ number_format($avg, 1) }}</strong> dari 5 ({{ $car->reviews_count }} ulasan)
                    @else
                        Belum ada ulasan.
                    @endif
                </p>

                <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-slate-400 text-xs">Tahun</dt><dd class="font-semibold">{{ $car->year }}</dd></div>
                    <div><dt class="text-slate-400 text-xs">Kursi</dt><dd class="font-semibold">{{ $car->seats }}</dd></div>
                    <div><dt class="text-slate-400 text-xs">Transmisi</dt><dd class="font-semibold">{{ ucfirst($car->transmission) }}</dd></div>
                    <div><dt class="text-slate-400 text-xs">Status</dt><dd class="font-semibold">{{ $car->status === 'available' ? 'Tersedia' : 'Tidak tersedia' }}</dd></div>
                </dl>

                @if ($car->description)
                    <p class="mt-4 text-sm text-slate-600">{{ $car->description }}</p>
                @endif

                @if ($car->status === 'available')
                    @auth
                        <a href="{{ route(auth()->user()->isStaffOrAdmin() ? 'admin.rentals.create' : 'customer.rentals.create', ['car_id' => $car->id]) }}"
                           class="mt-6 inline-flex px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm">Rental Mobil Ini</a>
                    @else
                        <a href="{{ route('login') }}" class="mt-6 inline-flex px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm">Masuk untuk Menyewa</a>
                    @endauth
                @endif
            </div>
        </div>

        <section class="mt-12">
            <h2 class="text-xl font-black text-slate-900 mb-4">Ulasan Pelanggan</h2>
            <div class="space-y-3">
                @forelse ($reviews as $review)
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-800">{{ $review->user->name ?? 'Pelanggan' }}</span>
                            <span class="text-xs text-slate-400">{{ $review->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="text-amber-500 tracking-widest">{{ str_repeat('★', $review->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $review->rating) }}</span></div>
                        @if ($review->comment)
                            <p class="mt-1 text-sm text-slate-600">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada ulasan untuk mobil ini.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $reviews->links() }}</div>
        </section>
    </div>
@endsection
