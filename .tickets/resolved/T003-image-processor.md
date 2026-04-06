# T003: Image Normalizer Service (Backend)

## USER NOTES:

The conversion happens on front-end, in the client's side. The backend will only be receiving the resized and finished webp format. Validation happens on client side. The concerns are resizing images that are above a certain threshold. Conversions happen purely on the frontend rather than being processed at backend. Use browser-image-compressor via `bun add browser-image-compressor`.

**Status:** ✅ IMPLEMENTED - Client-Side Processing  
**Implemented:** 2026-04-06  
**Implementation Notes:**
- **ALL image processing happens on the client-side** using browser-image-compression
- Installed `browser-image-compression@2.0.2` via bun
- Frontend automatically converts to WebP, resizes to max 2048px, and compresses (85% quality)
- Backend `ImageProcessor` service simplified to only store already-processed WebP data
- No PHP extensions required (GD/Imagick not needed)
- Client-side processing means:
  - Users see immediate feedback during compression
  - Server load reduced significantly
  - Metadata stripped automatically during WebP conversion
  - Network transfer optimized (only sends compressed WebP)

**Files Modified:**
- `resources/js/image-cropper.js` - Added browser-image-compression integration
- `app/Services/ImageProcessor.php` - Simplified to base64 storage only
- `app/Http/Controllers/PhotoController.php` - Updated to expect WebP base64 data
- Removed `intervention/image-laravel` dependency (not needed)

**Priority:** High  
**Type:** Feature  
**Estimated Effort:** Medium

## Summary

Implement **client-side** image processing (resize, compress, WebP conversion) using `browser-image-compression`, and simplify the backend to only store already-processed WebP base64 data.

## Current State

- Client-side processing is required by user notes
- Backend should only persist the processed WebP payload
- Server-side image libraries are unnecessary

## Requirements

From `.tickets/active/004-site-implementations.md`:
> TODO: Image normalizer should convert any uploads to webp format as well as strip the original metadata. Moreover, it should compress images when needed to mitigate high resolution and large high quality uploads.

## Implementation Steps

### 1. Install client-side compressor

```bash
bun add browser-image-compression
```

### 2. Update `resources/js/image-cropper.js`

- Compress selected images on the client (max 2048px, WebP, 85% quality)
- Feed compressed data into Cropper
- Export the cropped result to **WebP base64** on submit

### 3. Simplify `app/Services/ImageProcessor.php`

- Accept **only** WebP base64 input
- Decode and store to `storage/app/public/photos/`
- Generate unique `.webp` filenames

### 4. Update `App\Http\Controllers\PhotoController`

- Expect `photo` as WebP base64 data
- Store using `ImageProcessor`
- Delete old file when replacing

### 5. Tighten validation

- `StorePhotoRequest`: `photo` required, base64 WebP only
- `UpdatePhotoRequest`: `photo` optional, base64 WebP only when present

### 6. Remove server-side processing dependencies

- Remove `intervention/image-laravel` from Composer
- No GD/Imagick required

## Files to Create/Modify

| File | Action |
|------|--------|
| `package.json` | Modify (add browser-image-compression) |
| `resources/js/image-cropper.js` | Modify |
| `app/Services/ImageProcessor.php` | Create/Modify |
| `app/Http/Controllers/PhotoController.php` | Modify |
| `app/Http/Requests/StorePhotoRequest.php` | Modify |
| `app/Http/Requests/UpdatePhotoRequest.php` | Modify |

## Acceptance Criteria

 - [ ] All uploads are processed on the client and sent as WebP base64
 - [ ] Images larger than 2048x2048 are resized client-side
 - [ ] Compression uses ~85% quality WebP
 - [ ] EXIF metadata is stripped during client conversion
 - [ ] Backend stores WebP files without additional processing
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
 - Client-side processing removes the need for GD/Imagick
 - The service is designed for dependency injection (Laravel auto-resolves it)
 - Quality of 85 is a good balance between file size and visual quality
 - Resizing happens before cropping to reduce memory usage
