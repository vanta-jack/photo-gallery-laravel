<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * ImageProcessor Service
 * 
 * Handles storage of client-processed images.
 * All image processing (WebP conversion, resizing, compression) happens on the client-side.
 * This service simply stores the already-processed WebP base64 data.
 */
class ImageProcessor
{
    /**
     * Process and store a base64 WebP image from the client.
     * 
     * @param string $base64Data Base64 data URI string (already WebP format from client)
     * @param string $directory Storage directory (relative to public disk)
     * @return string The storage path of the WebP image
     */
    public function process(string $base64Data, string $directory = 'photos'): string
    {
        // Extract base64 data from data URI
        // Format: data:image/webp;base64,{base64data}
        if (!str_starts_with($base64Data, 'data:image/webp;base64,')) {
            throw new \InvalidArgumentException('Invalid base64 WebP data');
        }
        
        // Split on comma to get the actual base64 content
        $parts = explode(',', $base64Data, 2);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Invalid base64 data format');
        }
        
        $imageData = base64_decode($parts[1], true);
        
        if ($imageData === false) {
            throw new \InvalidArgumentException('Failed to decode base64 image data');
        }
        
        // Generate unique filename
        $filename = $this->generateFilename();
        $path = $directory . '/' . $filename;
        
        // Store the image
        Storage::disk('public')->put($path, $imageData);
        
        return $path;
    }

    /**
     * Process and store in user-specific directory.
     * 
     * @param string $base64Data Base64 data URI string
     * @param int $userId User ID for directory organization
     * @return string The storage path of the WebP image
     */
    public function processForUser(string $base64Data, int $userId): string
    {
        return $this->process($base64Data, "photos/{$userId}");
    }

    /**
     * Generate a unique filename for the image.
     */
    protected function generateFilename(): string
    {
        return uniqid('img_', true) . '.webp';
    }
}
