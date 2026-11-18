<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Convert an uploaded screenshot to WebP format and save to public storage.
     *
     * @return string Relative storage path (e.g. trades/filename.webp)
     */
    public function convertAndStoreAsWebp(UploadedFile $file, string $folder = 'trades', int $quality = 82): string
    {
        // Ensure storage directory exists
        $storageDir = storage_path("app/public/{$folder}");
        if (! file_exists($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $filename = 'trade_'.Str::random(16).'_'.time().'.webp';
        $fullPath = $storageDir.DIRECTORY_SEPARATOR.$filename;
        $relativePath = "{$folder}/{$filename}";

        $fileContent = file_get_contents($file->getRealPath());

        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $image = @imagecreatefromstring($fileContent);

            if ($image !== false) {
                // Ensure proper alpha handling for PNG/WebP transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);

                $saved = @imagewebp($image, $fullPath, $quality);
                imagedestroy($image);

                if ($saved && file_exists($fullPath)) {
                    return $relativePath;
                }
            }
        }

        // Fallback if GD is unavailable or fails to read
        $storedPath = $file->store($folder, 'public');

        return $storedPath;
    }

    /**
     * Delete an existing screenshot if present.
     */
    public function deleteScreenshot(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
