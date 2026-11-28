<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PublicStorageService
{
    /**
     * Simpan file ke disk public dan salin ke public/storage jika bukan symlink.
     */
    public static function store(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');

        $publicStorageDir = public_path('storage');
        if (! is_link($publicStorageDir)) {
            $targetPath = $publicStorageDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
            File::ensureDirectoryExists(dirname($targetPath));
            File::copy(Storage::disk('public')->path($path), $targetPath);
        }

        return $path;
    }

    /**
     * Hapus file dari disk public dan salinan di public/storage.
     */
    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('public')->delete($path);

        $publicTarget = public_path('storage/' . ltrim($path, '/'));
        if (! is_link(public_path('storage')) && File::exists($publicTarget)) {
            File::delete($publicTarget);
        }
    }
}
