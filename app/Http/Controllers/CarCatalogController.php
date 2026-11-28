<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\View\View;

class CarCatalogController extends Controller
{
    /**
     * Halaman detail mobil (publik) beserta ulasan customer.
     */
    public function show(Car $car): View
    {
        $car->load(['category', 'images'])->loadAvg('reviews', 'rating')->loadCount('reviews');

        $reviews = $car->reviews()
            ->with('user:id,name')
            ->latestFirst()
            ->paginate(10);

        return view('cars.show', compact('car', 'reviews'));
    }
}
