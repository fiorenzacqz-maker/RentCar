<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageController extends Controller
{
    /**
     * Tampilkan halaman utama (homepage) dengan katalog mobil dari database.
     */
    public function index(): View
    {
        // Ambil kategori mobil untuk filter
        $categories = CarCategory::orderBy('name', 'asc')->get();

        // Ambil daftar mobil yang berstatus available dari database beserta relasi kategori dan galeri foto
        $cars = Car::with(['category', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'available')
            ->orderBy('id', 'asc')
            ->get();

        return view('homepage', compact('cars', 'categories'));
    }
}
