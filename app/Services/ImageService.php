<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Get the active storage disk ('s3' or 'public').
     */
    public function getDisk(): string
    {
        $default = config('filesystems.default');

        return in_array($default, ['s3', 'public'], true) ? $default : 'public';
    }

    /**
     * Convert an uploaded screenshot to WebP format and save to the configured storage disk.
     *
     * @return string Relative storage path (e.g. trades/filename.webp)
     */
    public function convertAndStoreAsWebp(UploadedFile $file, string $folder = 'trades', int $quality = 82): string
    {
        $disk = $this->getDisk();
        $filename = 'trade_'.Str::random(16).'_'.time().'.webp';
        $relativePath = "{$folder}/{$filename}";

        $fileContent = file_get_contents($file->getRealPath());

        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $image = @imagecreatefromstring($fileContent);

            if ($image !== false) {
                // Ensure proper alpha handling for PNG/WebP transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);

                $tempPath = tempnam(sys_get_temp_dir(), 'trade_webp_');
                $saved = @imagewebp($image, $tempPath, $quality);
                imagedestroy($image);

                if ($saved && file_exists($tempPath)) {
                    $webpContents = file_get_contents($tempPath);
                    @unlink($tempPath);

                    $stored = Storage::disk($disk)->put($relativePath, $webpContents);

                    if ($stored) {
                        return $relativePath;
                    }
                }
            }
        }

        // Fallback if GD is unavailable or fails to convert
        return $file->store($folder, $disk);
    }

    /**
     * Convert an uploaded profile avatar to a square WebP image and save to storage.
     *
     * @return string Relative storage path (e.g. avatars/filename.webp)
     */
    public function convertAndStoreAvatar(UploadedFile $file, int $maxSize = 400, int $quality = 85): string
    {
        $disk = $this->getDisk();
        $filename = 'avatar_'.Str::random(16).'_'.time().'.webp';
        $relativePath = "avatars/{$filename}";

        $fileContent = file_get_contents($file->getRealPath());

        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $image = @imagecreatefromstring($fileContent);

            if ($image !== false) {
                $origWidth = imagesx($image);
                $origHeight = imagesy($image);

                // Center crop to square
                $minDim = min($origWidth, $origHeight);
                $srcX = (int) (($origWidth - $minDim) / 2);
                $srcY = (int) (($origHeight - $minDim) / 2);

                $targetSize = min($minDim, $maxSize);
                $resized = imagecreatetruecolor($targetSize, $targetSize);

                imagealphablending($resized, false);
                imagesavealpha($resized, true);

                imagecopyresampled($resized, $image, 0, 0, $srcX, $srcY, $targetSize, $targetSize, $minDim, $minDim);
                imagedestroy($image);

                $tempPath = tempnam(sys_get_temp_dir(), 'avatar_webp_');
                $saved = @imagewebp($resized, $tempPath, $quality);
                imagedestroy($resized);

                if ($saved && file_exists($tempPath)) {
                    $webpContents = file_get_contents($tempPath);
                    @unlink($tempPath);

                    $stored = Storage::disk($disk)->put($relativePath, $webpContents);

                    if ($stored) {
                        return $relativePath;
                    }
                }
            }
        }

        return $file->store('avatars', $disk);
    }

    /**
     * Delete an existing avatar from storage.
     */
    public function deleteAvatar(?string $path): void
    {
        $this->deleteScreenshot($path);
    }

    /**
     * Delete an existing screenshot from storage.
     */
    public function deleteScreenshot(?string $path): void
    {
        if (! $path) {
            return;
        }

        $disk = $this->getDisk();

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
