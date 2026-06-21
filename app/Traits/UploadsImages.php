<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

trait UploadsImages
{
    protected function uploadAsWebp(UploadedFile $file, string $directory, int $quality = 82): string
    {
        if (in_array($file->getMimeType(), ['image/svg+xml', 'image/gif'])) {
            return $file->store($directory, 'public');
        }

        $path = $directory . '/' . Str::uuid() . '.webp';
        $encoded = Image::decode($file)->encode(new WebpEncoder(quality: $quality));
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
