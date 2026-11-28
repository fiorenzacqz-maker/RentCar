@extends('layouts.dashboard')

@section('title', 'Beri Ulasan')
@section('page-title', 'Beri Ulasan')

@section('content')
    <div class="mb-6">
        <a href="{{ route('customer.rentals.show', $booking) }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Detail Booking
        </a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Ulasan untuk {{ $booking->car->brand }} {{ $booking->car->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">Booking #{{ $booking->id }} &middot; {{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm max-w-xl">
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.reviews.store', $booking) }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rating *</span>
                <div class="flex flex-row-reverse justify-end gap-1">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" class="peer sr-only" {{ (int) old('rating') === $i ? 'checked' : '' }} required>
                        <label for="rating{{ $i }}" title="{{ $i }} bintang"
                            class="text-3xl cursor-pointer text-slate-300 peer-checked:text-amber-500 hover:text-amber-400 peer-hover:text-amber-400">&#9733;</label>
                    @endfor
                </div>
            </div>

            <div>
                <label for="comment" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ulasan <span class="text-slate-400 font-normal lowercase">(opsional)</span></label>
                <textarea name="comment" id="comment" rows="4" maxlength="1000" placeholder="Ceritakan pengalaman Anda menyewa mobil ini..."
                    class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 resize-none">{{ old('comment') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('customer.rentals.show', $booking) }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl cursor-pointer">Kirim Ulasan</button>
            </div>
        </form>
    </div>
@endsection
