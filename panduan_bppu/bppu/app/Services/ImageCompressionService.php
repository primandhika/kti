<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageCompressionService
{
    /**
     * Compress image like WhatsApp style using native GD library
     * Target: 50-100KB per image
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param int $maxWidth Maximum width in pixels (default 800px)
     * @param int $quality Initial quality (will auto-adjust)
     * @param int $targetSizeKB Target file size in KB
     * @return string Path to compressed image
     */
    public function compressAndStore($file, $directory = 'menu-images', $maxWidth = 800, $quality = 85, $targetSizeKB = 100)
    {
        // Check file size - skip compression if already small (<150KB)
        $originalSizeKB = $file->getSize() / 1024;
        if ($originalSizeKB < 150) {
            // Just store the original file
            $filename = uniqid('menu_') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $storagePath = $directory . '/' . $filename;
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            return $storagePath;
        }

        // Create image resource from uploaded file
        $sourceImage = $this->createImageFromFile($file);
        if (!$sourceImage) {
            throw new \Exception('Failed to create image resource');
        }

        // Get original dimensions
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // Calculate new dimensions (maintain aspect ratio)
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;

        if ($originalWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) ($originalHeight * ($maxWidth / $originalWidth));
        }

        // Create new image with calculated dimensions
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Set white background (for transparent PNGs)
        $white = imagecolorallocate($resizedImage, 255, 255, 255);
        imagefill($resizedImage, 0, 0, $white);

        // Enable high quality resampling
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Auto-adjust quality to meet target size
        $tempPath = sys_get_temp_dir() . '/' . uniqid('img_') . '.jpg';
        $attempts = 0;
        $maxAttempts = 8;

        while ($attempts < $maxAttempts) {
            // Save with current quality
            imagejpeg($resizedImage, $tempPath, $quality);

            // Check file size
            $fileSizeKB = filesize($tempPath) / 1024;

            // If size is acceptable, break
            if ($fileSizeKB <= $targetSizeKB) {
                break;
            }

            // Reduce quality for next attempt
            $quality -= 10;
            if ($quality < 20) {
                $quality = 20; // Minimum quality
                break;
            }

            $attempts++;
        }

        // If still too large, do aggressive resize
        if (filesize($tempPath) / 1024 > $targetSizeKB && $maxWidth > 400) {
            imagedestroy($resizedImage);

            // Recalculate dimensions for smaller size
            $smallerWidth = 400;
            $smallerHeight = (int) ($originalHeight * ($smallerWidth / $originalWidth));

            $resizedImage = imagecreatetruecolor($smallerWidth, $smallerHeight);
            $white = imagecolorallocate($resizedImage, 255, 255, 255);
            imagefill($resizedImage, 0, 0, $white);
            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $smallerWidth, $smallerHeight, $originalWidth, $originalHeight);

            imagejpeg($resizedImage, $tempPath, 75);
        }

        // Generate unique filename
        $filename = uniqid('menu_') . '_' . time() . '.jpg';
        $storagePath = $directory . '/' . $filename;

        // Store to public disk
        Storage::disk('public')->put($storagePath, file_get_contents($tempPath));

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        @unlink($tempPath);

        return $storagePath;
    }

    /**
     * Create GD image resource from uploaded file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return resource|false
     */
    private function createImageFromFile($file)
    {
        $mimeType = $file->getMimeType();

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagecreatefromjpeg($file->getPathname());

            case 'image/png':
                return imagecreatefrompng($file->getPathname());

            case 'image/gif':
                return imagecreatefromgif($file->getPathname());

            case 'image/webp':
                return imagecreatefromwebp($file->getPathname());

            default:
                return false;
        }
    }

    /**
     * Get file size in KB
     *
     * @param string $path Storage path (without 'public/')
     * @return float Size in KB
     */
    public function getFileSizeKB($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->size($path) / 1024;
        }
        return 0;
    }

    /**
     * Delete image from storage
     *
     * @param string $url Full URL or storage path
     * @return bool
     */
    public function deleteImage($url)
    {
        if (!$url) return false;

        // Extract path from URL
        $path = str_replace('/storage/', '', $url);

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
