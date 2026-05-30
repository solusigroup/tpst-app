<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Compress and resize an uploaded image, then store it.
     * Fallback to normal upload if not an image or if GD extension is not available.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @param int $quality
     * @param int $maxWidth
     * @return string|false
     */
    public static function compressAndStore(UploadedFile $file, string $directory, string $disk = 'public', int $quality = 70, int $maxWidth = 1200)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        // Fallback to normal upload if not a supported image type or if GD is missing
        if (!in_array($extension, $allowedExtensions) || !extension_loaded('gd')) {
            return $file->store($directory, $disk);
        }

        $realPath = $file->getRealPath();
        $imageInfo = @getimagesize($realPath);
        if (!$imageInfo) {
            return $file->store($directory, $disk);
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Calculate new dimensions if image is larger than maxWidth
        if ($width > $maxWidth) {
            $height = (int) floor($height * ($maxWidth / $width));
            $width = $maxWidth;
        }

        // Create image resource based on mime type
        switch ($mime) {
            case 'image/jpeg':
                $src = @imagecreatefromjpeg($realPath);
                break;
            case 'image/png':
                $src = @imagecreatefrompng($realPath);
                break;
            case 'image/webp':
                $src = @imagecreatefromwebp($realPath);
                break;
            case 'image/gif':
                $src = @imagecreatefromgif($realPath);
                break;
            default:
                return $file->store($directory, $disk);
        }

        if (!$src) {
            return $file->store($directory, $disk);
        }

        // Create blank canvas
        $dst = imagecreatetruecolor($width, $height);

        // Maintain transparency for PNG and WebP
        if (in_array($mime, ['image/png', 'image/webp'])) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $width, $height, $transparent);
        }

        // Resize
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $imageInfo[0], $imageInfo[1]);

        // Generate unique filename
        $filename = Str::random(40) . '.' . $extension;
        $tempPath = sys_get_temp_dir() . '/' . $filename;

        // Save image to temp path with compression
        $saved = false;
        switch ($mime) {
            case 'image/jpeg':
                $saved = @imagejpeg($dst, $tempPath, $quality);
                break;
            case 'image/png':
                // PNG quality is 0 (no compression) to 9
                $pngQuality = (int) round((100 - $quality) / 10);
                $pngQuality = max(0, min(9, $pngQuality));
                $saved = @imagepng($dst, $tempPath, $pngQuality);
                break;
            case 'image/webp':
                $saved = @imagewebp($dst, $tempPath, $quality);
                break;
            case 'image/gif':
                $saved = @imagegif($dst, $tempPath);
                break;
        }

        imagedestroy($src);
        imagedestroy($dst);

        if (!$saved || !file_exists($tempPath)) {
            return $file->store($directory, $disk);
        }

        // Store to Laravel Storage
        $path = $directory . '/' . $filename;
        Storage::disk($disk)->put($path, file_get_contents($tempPath));
        @unlink($tempPath);

        return $path;
    }
}
