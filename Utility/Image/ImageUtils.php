<?php

declare(strict_types=1);

namespace Utility\Image;

use Exception;
use GdImage;
use InvalidArgumentException;
use RuntimeException;

class ImageUtils
{
    private const MAX_FILE_SIZE = 5242880; // 5MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_DIMENSION = 2000;


    public function uploadImage(array $data, string $uploadPath, int $quality): array
    {
        try {
            if (!isset($data['image']) || empty($data['image']['tmp_name'])) {
                throw new InvalidArgumentException('No image file provided');
            }

            $file = $data['image'];
            
            // Validate file
            ImageUtils::validateFile($file);
            
            // Create upload directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Generate unique filename
            $filename = uniqid('img_') . '_' . time() . '.webp';
            $filepath = $uploadPath . $filename;
            
            // Process and optimize image
            $sourceImage = ImageUtils::createImageResource($file['tmp_name']);
            
            // Get original dimensions
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);
            
            // Calculate new dimensions if needed
            list($newWidth, $newHeight) = ImageUtils::calculateDimensions(
                $originalWidth, 
                $originalHeight
            );
            
            // Create new image with calculated dimensions
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency
            imagepalettetotruecolor($newImage);
            imagealphablending($newImage, true);
            imagesavealpha($newImage, true);
            
            // Resize image
            imagecopyresampled(
                $newImage, 
                $sourceImage,
                0, 0, 0, 0,
                $newWidth, 
                $newHeight, 
                $originalWidth, 
                $originalHeight
            );
            
            // Save as WebP
            imagewebp($newImage, $filepath, $quality);
            
            // Clean up
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public static function validateFile(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed with error code: ' . $file['error']);
        }
        
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new RuntimeException('File size exceeds limit of 5MB');
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            throw new RuntimeException('Invalid file type. Allowed types: JPEG, PNG, WebP');
        }
    }
    
    public static function createImageResource(string $filepath): GdImage
    {
        $mimeType = mime_content_type($filepath);
        
        return match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($filepath),
            'image/png' => imagecreatefrompng($filepath),
            'image/webp' => imagecreatefromwebp($filepath),
            default => throw new RuntimeException('Unsupported image type')
        };
    }
    
    public static function calculateDimensions(int $width, int $height): array
    {
        if ($width <= self::MAX_DIMENSION && $height <= self::MAX_DIMENSION) {
            return [$width, $height];
        }
        
        $ratio = $width / $height;
        
        if ($width > $height) {
            $newWidth = self::MAX_DIMENSION;
            $newHeight = (int) (self::MAX_DIMENSION / $ratio);
        } else {
            $newHeight = self::MAX_DIMENSION;
            $newWidth = (int) (self::MAX_DIMENSION * $ratio);
        }
        
        return [$newWidth, $newHeight];
    }
} 