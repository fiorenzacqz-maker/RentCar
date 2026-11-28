<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori mobil.
     */
    public function index(): View
    {
        $categories = CarCategory::withCount('cars')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Simpan kategori mobil baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:car_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori ini sudah ada.',
        ]);

        CarCategory::create($validated);

        return back()->with('success', 'Kategori mobil berhasil ditambahkan.');
    }

    /**
     * Perbarui kategori mobil.
     */
    public function update(Request $request, CarCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:car_categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
        ]);

        $category->update($validated);

        return back()->with('success', 'Kategori mobil berhasil diperbarui.');
    }

    /**
     * Hapus kategori mobil jika tidak memiliki mobil.
     */
    public function destroy(CarCategory $category): RedirectResponse
    {
        if ($category->cars()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data mobil.');
        }

        $category->delete();

        return back()->with('success', 'Kategori mobil berhasil dihapus.');
    }
}
