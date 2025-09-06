<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Compress and resize image
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality
     * @return string|null
     */
    public static function compressImage($file, $maxWidth = 800, $maxHeight = 600, $quality = 80)
    {
        try {
            // Check if file is valid image
            if (!$file->isValid() || !in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                return null;
            }

            // Get file info
            $originalPath = $file->getPathname();
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Create image resource based on extension
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($originalPath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($originalPath);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($originalPath);
                    break;
                default:
                    return null;
            }

            if (!$image) {
                return null;
            }

            // Get original dimensions
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Calculate new dimensions maintaining aspect ratio
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);

            // Create new image with new dimensions
            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG
            if ($extension === 'png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize image
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Generate compressed file path
            $compressedPath = sys_get_temp_dir() . '/' . uniqid('compressed_') . '.jpg';

            // Save compressed image as JPEG
            $result = imagejpeg($newImage, $compressedPath, $quality);

            // Clean up memory
            imagedestroy($image);
            imagedestroy($newImage);

            if ($result) {
                return $compressedPath;
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('Image compression error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get file size in MB
     *
     * @param string $filePath
     * @return float
     */
    public static function getFileSizeInMB($filePath)
    {
        return round(filesize($filePath) / 1024 / 1024, 2);
    }

    /**
     * Validate image file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxSizeMB
     * @return array
     */
    public static function validateImage($file, $maxSizeMB = 5)
    {
        $errors = [];

        // Check if file exists
        if (!$file || !$file->isValid()) {
            $errors[] = 'Invalid file uploaded';
            return $errors;
        }

        // Check file size
        $fileSizeMB = self::getFileSizeInMB($file->getPathname());
        if ($fileSizeMB > $maxSizeMB) {
            $errors[] = "File size ({$fileSizeMB}MB) exceeds maximum allowed size ({$maxSizeMB}MB)";
        }

        // Check file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'Invalid file type. Only JPG, PNG, and GIF files are allowed';
        }

        // Check MIME type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Invalid file format';
        }

        return $errors;
    }
}
