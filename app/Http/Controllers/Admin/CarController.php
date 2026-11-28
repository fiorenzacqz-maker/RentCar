<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarImage;
use App\Services\PublicStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarController extends Controller
{
    /**
     * Tampilkan daftar semua mobil untuk admin.
     */
    public function index(): View
    {
        $cars = Car::with('category')->latest()->paginate(10);

        return view('admin.cars.index', compact('cars'));
    }

    /**
     * Tampilkan form tambah mobil baru.
     */
    public function create(): View
    {
        $categories = CarCategory::orderBy('name')->get();

        return view('admin.cars.create', compact('categories'));
    }

    /**
     * Simpan data mobil baru beserta foto utama dan galeri opsional.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:car_categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:100'],
            'plate_number' => ['required', 'string', 'max:20', 'unique:cars,plate_number'],
            'year' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'transmission' => ['required', 'in:manual,automatic'],
            'seats' => ['required', 'integer', 'min:2', 'max:20'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,unavailable,maintenance'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['image'] = PublicStorageService::store($request->file('image'), 'cars');

        $car = Car::create($validated);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = PublicStorageService::store($file, 'cars/gallery');
                CarImage::create([
                    'car_id' => $car->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.cars.index')->with('success', 'Data mobil berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit data mobil.
     */
    public function edit(Car $car): View
    {
        $car->load('images');
        $categories = CarCategory::orderBy('name')->get();

        return view('admin.cars.edit', compact('car', 'categories'));
    }

    /**
     * Perbarui data mobil.
     */
    public function update(Request $request, Car $car): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:car_categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:100'],
            'plate_number' => ['required', 'string', 'max:20', 'unique:cars,plate_number,' . $car->id],
            'year' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'transmission' => ['required', 'in:manual,automatic'],
            'seats' => ['required', 'integer', 'min:2', 'max:20'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,unavailable,maintenance'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            PublicStorageService::delete($car->image);
            $validated['image'] = PublicStorageService::store($request->file('image'), 'cars');
        } else {
            unset($validated['image']);
        }

        $car->update($validated);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = PublicStorageService::store($file, 'cars/gallery');
                CarImage::create([
                    'car_id' => $car->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.cars.index')->with('success', 'Data mobil berhasil diperbarui.');
    }

    /**
     * Hapus data mobil jika belum memiliki riwayat booking.
     */
    public function destroy(Car $car): RedirectResponse
    {
        if ($car->bookings()->exists()) {
            return back()->with('error', 'Mobil tidak dapat dihapus karena sudah memiliki riwayat booking. Ubah status menjadi unavailable jika tidak disewakan.');
        }

        PublicStorageService::delete($car->image);
        foreach ($car->images as $img) {
            PublicStorageService::delete($img->image_path);
        }

        $car->delete();

        return redirect()->route('admin.cars.index')->with('success', 'Data mobil berhasil dihapus.');
    }
}
