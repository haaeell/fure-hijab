<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Kompres, resize, dan simpan gambar sebagai WebP ke storage/public.
     * SVG disimpan apa adanya tanpa diproses.
     *
     * @return string  path relatif di disk public (misal "products/uuid.webp")
     */
    public static function compress(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1200,
        int $quality = 82
    ): string {
        if ($file->getMimeType() === 'image/svg+xml') {
            return $file->store($directory, 'public');
        }

        $path = $directory . '/' . Str::uuid() . '.webp';

        Storage::disk('public')->put(
            $path,
            Image::decode($file->getRealPath())
                ->scaleDown(width: $maxWidth)
                ->toWebp(quality: $quality)
        );

        return $path;
    }
}
