<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

trait UploadsImages
{
    protected function uploadAsWebp(UploadedFile $file, string $directory, int $quality = 82): string
    {
        if (in_array($file->getMimeType(), ['image/svg+xml', 'image/gif'])) {
            return $file->store($directory, 'public');
        }

        $path = $directory . '/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($path, Image::read($file)->toWebp($quality));

        return $path;
    }
}
