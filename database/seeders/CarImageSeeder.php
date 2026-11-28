<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CarImageSeeder extends Seeder
{
    /**
     * Seed car_images table (galeri foto mobil) dan pastikan file gambar tersedia.
     */
    public function run(): void
    {
        $cars = Car::all();

        foreach ($cars as $car) {
            // Foto Utama Mobil
            CarImage::updateOrCreate(
                [
                    'car_id' => $car->id,
                    'image_path' => $car->image,
                ]
            );

            // Foto Tambahan / Interior
            $interiorPath = 'cars/' . strtolower($car->name) . '_interior.jpg';
            $this->ensureDummyImage($interiorPath, $car->brand . ' ' . $car->name . ' - Interior');

            CarImage::updateOrCreate(
                [
                    'car_id' => $car->id,
                    'image_path' => $interiorPath,
                ]
            );
        }
    }

    /**
     * Buat gambar JPG dummy jika file belum ada di storage & public/storage.
     */
    private function ensureDummyImage(string $relativePath, string $label): void
    {
        $storagePath = Storage::disk('public')->path($relativePath);
        $publicPath = public_path('storage/' . $relativePath);

        if (! File::exists($storagePath)) {
            File::ensureDirectoryExists(dirname($storagePath));

            if (extension_loaded('gd')) {
                $img = imagecreatetruecolor(800, 500);
                $bg = imagecolorallocate($img, 15, 37, 87);
                $accent = imagecolorallocate($img, 37, 99, 235);
                $white = imagecolorallocate($img, 255, 255, 255);
                $muted = imagecolorallocate($img, 191, 219, 254);

                imagefilledrectangle($img, 0, 0, 800, 500, $bg);
                imagefilledrectangle($img, 0, 0, 800, 14, $accent);
                imagestring($img, 5, 40, 210, 'RENTCAR GALLERY', $muted);
                imagestring($img, 5, 40, 245, $label, $white);
                imagejpeg($img, $storagePath, 85);
                imagedestroy($img);
            }
        }

        if (File::exists($storagePath) && ! is_link(public_path('storage')) && ! File::exists($publicPath)) {
            File::ensureDirectoryExists(dirname($publicPath));
            File::copy($storagePath, $publicPath);
        }
    }
}
