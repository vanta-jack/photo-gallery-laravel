# T003: Image Normalizer Service (Backend)

**Priority:** High  
**Type:** Feature  
**Estimated Effort:** Medium

## Summary

Create `App\Services\ImageProcessor` service to convert uploaded images to WebP format, strip EXIF metadata, and compress large images.

## Current State

- `app/Services/` directory does not exist
- `PhotoController` stores uploaded files directly to `storage/app/public/photos/` without processing
- No WebP conversion occurs
- EXIF metadata (location, camera info, etc.) remains in uploaded images
- Large high-resolution uploads are stored as-is

## Requirements

From `.tickets/active/004-site-implementations.md`:
> TODO: Image normalizer should convert any uploads to webp format as well as strip the original metadata. Moreover, it should compress images when needed to mitigate high resolution and large high quality uploads.

## Implementation Steps

### 1. Install Intervention Image

```bash
composer require intervention/image-laravel
```

This package is the standard for image manipulation in Laravel. Version 3.x supports Laravel 10+ and PHP 8.1+.

### 2. Create `app/Services/ImageProcessor.php`

```php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Interfaces\EncodedImageInterface;

/**
 * ImageProcessor Service
 * 
 * Handles image normalization:
 * - Converts all images to WebP format
 * - Strips EXIF and other metadata
 * - Resizes large images to maximum dimensions
 * - Compresses output to configured quality
 */
class ImageProcessor
{
    /**
     * Maximum width for processed images.
     */
    protected int $maxWidth = 2048;
    
    /**
     * Maximum height for processed images.
     */
    protected int $maxHeight = 2048;
    
    /**
     * WebP quality (1-100).
     */
    protected int $quality = 85;

    /**
     * Process an uploaded file or base64 string.
     * 
     * @param UploadedFile|string $input Either an UploadedFile or base64 data URI string
     * @param string $directory Storage directory (relative to public disk)
     * @return string The storage path of the processed WebP image
     */
    public function process(UploadedFile|string $input, string $directory = 'photos'): string
    {
        // Read image from input
        if (is_string($input)) {
            // Handle base64 data URI from cropper
            $image = Image::read($input);
        } else {
            // Handle UploadedFile
            $image = Image::read($input->path());
        }
        
        // Resize if exceeds max dimensions (maintains aspect ratio)
        // scaleDown only shrinks, never enlarges
        $image->scaleDown(width: $this->maxWidth, height: $this->maxHeight);
        
        // Convert to WebP format
        // This automatically strips EXIF and other metadata
        $encoded = $image->toWebp($this->quality);
        
        // Generate unique filename
        $filename = $this->generateFilename();
        $path = $directory . '/' . $filename;
        
        // Store the processed image
        Storage::disk('public')->put($path, (string) $encoded);
        
        return $path;
    }

    /**
     * Process and store in user-specific directory.
     * 
     * @param UploadedFile|string $input Either an UploadedFile or base64 data URI string
     * @param int $userId User ID for directory organization
     * @return string The storage path of the processed WebP image
     */
    public function processForUser(UploadedFile|string $input, int $userId): string
    {
        return $this->process($input, "photos/{$userId}");
    }

    /**
     * Generate a unique filename for the processed image.
     */
    protected function generateFilename(): string
    {
        return uniqid('img_', true) . '.webp';
    }

    /**
     * Set maximum dimensions for processed images.
     */
    public function setMaxDimensions(int $width, int $height): self
    {
        $this->maxWidth = $width;
        $this->maxHeight = $height;
        return $this;
    }

    /**
     * Set quality for WebP output (1-100).
     */
    public function setQuality(int $quality): self
    {
        $this->quality = max(1, min(100, $quality));
        return $this;
    }
}
```

### 3. Update `App\Http\Controllers\PhotoController`

Inject and use the ImageProcessor service:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Services\ImageProcessor;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /**
     * Store a newly created photo in storage.
     */
    public function store(StorePhotoRequest $request, ImageProcessor $imageProcessor): RedirectResponse
    {
        // Determine input source (base64 from cropper or file upload)
        $input = $this->getPhotoInput($request);
        
        if ($input === null) {
            return back()->withErrors(['photo' => 'An image is required.']);
        }
        
        // Process image (converts to WebP, strips metadata, resizes)
        $path = $imageProcessor->process($input);

        $photo = Photo::create([
            'user_id' => $request->user()->id,
            'path' => $path,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Photo uploaded.');
    }

    /**
     * Update the specified photo in storage.
     */
    public function update(UpdatePhotoRequest $request, Photo $photo, ImageProcessor $imageProcessor): RedirectResponse
    {
        $this->authorize('update', $photo);

        $data = $request->validated();

        // Check if a new photo was provided
        $input = $this->getPhotoInput($request);
        
        if ($input !== null) {
            // Delete old file
            Storage::disk('public')->delete($photo->path);
            
            // Process and store new image
            $data['path'] = $imageProcessor->process($input);
        }

        $photo->update($data);

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Photo updated.');
    }

    /**
     * Get photo input from request (base64 or file).
     */
    protected function getPhotoInput($request): UploadedFile|string|null
    {
        // Check for base64 data from cropper
        $photoData = $request->input('photo');
        if (is_string($photoData) && str_starts_with($photoData, 'data:image')) {
            return $photoData;
        }
        
        // Check for file upload
        if ($request->hasFile('photo')) {
            return $request->file('photo');
        }
        
        return null;
    }

    // ... rest of existing methods (index, create, show, edit, destroy) remain unchanged
}
```

### 4. Update `App\Http\Requests\StorePhotoRequest.php`

Allow either file or string (base64) input:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Accept either string (base64) or file
            'photo' => ['required'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'An image is required.',
        ];
    }
}
```

### 5. Update `App\Http\Requests\UpdatePhotoRequest.php`

Similarly allow optional photo replacement:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Photo is optional on update
            'photo' => ['nullable'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
```

### 6. Verify GD or Imagick Extension

Intervention Image requires either GD or Imagick PHP extension. Check with:

```bash
php -m | grep -E "gd|imagick"
```

GD is typically included by default. If using Imagick, ensure it supports WebP.

## Files to Create/Modify

| File | Action |
|------|--------|
| `composer.json` | Modify (add intervention/image-laravel) |
| `app/Services/ImageProcessor.php` | Create |
| `app/Http/Controllers/PhotoController.php` | Modify |
| `app/Http/Requests/StorePhotoRequest.php` | Modify |
| `app/Http/Requests/UpdatePhotoRequest.php` | Modify |

## Acceptance Criteria

- [ ] All uploaded images are converted to WebP format
- [ ] EXIF metadata is stripped (verify with `exiftool` or similar)
- [ ] Images larger than 2048x2048 are scaled down
- [ ] Aspect ratio is preserved during scaling
- [ ] Service works with both file uploads and base64 input
- [ ] Old photos are deleted when replaced during update
- [ ] Processed images are stored in `storage/app/public/photos/`

## Dependencies

None - this is a foundation service.

## Blocks

- T012 (AI Generator) - needs ImageProcessor to save generated images
- T017 (Multi-upload) - needs ImageProcessor for batch processing
- T018 (User Directories) - extends ImageProcessor usage

## Notes

- WebP provides ~30% smaller file sizes than JPEG at similar quality
- Intervention Image v3 automatically strips metadata during encoding
- The service is designed for dependency injection (Laravel auto-resolves it)
- Quality of 85 is a good balance between file size and visual quality
- `scaleDown()` only shrinks images, never enlarges them (prevents upscaling artifacts)
