<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Form ulasan untuk booking yang sudah selesai.
     */
    public function create(Request $request, Booking $booking): View|RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($error = $this->cannotReview($booking)) {
            return redirect()->route('customer.rentals.show', $booking)->with('error', $error);
        }

        $booking->load('car');

        return view('customer.reviews.create', compact('booking'));
    }

    /**
     * Simpan ulasan (satu ulasan per booking).
     */
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($error = $this->cannotReview($booking)) {
            return redirect()->route('customer.rentals.show', $booking)->with('error', $error);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Pilih rating bintang 1 sampai 5.',
            'rating.between' => 'Rating harus antara 1 sampai 5.',
        ]);

        $review = Review::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => $request->user()->id,
                'car_id' => $booking->car_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        if (! $review->wasRecentlyCreated) {
            return redirect()->route('customer.rentals.show', $booking)->with('error', 'Anda sudah memberikan ulasan untuk booking ini.');
        }

        return redirect()->route('customer.rentals.show', $booking)->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }

    private function authorizeBooking(Request $request, Booking $booking): void
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function cannotReview(Booking $booking): ?string
    {
        if ($booking->booking_status !== 'completed') {
            return 'Ulasan hanya dapat diberikan setelah sewa selesai (completed).';
        }

        if ($booking->review()->exists()) {
            return 'Anda sudah memberikan ulasan untuk booking ini.';
        }

        return null;
    }
}
