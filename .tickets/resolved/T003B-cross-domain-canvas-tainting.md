# T003B: Local-network upload reliability + WebP fallback behavior

**Status:** Pending Review  
**Created:** 2026-04-06  
**Priority:** Critical  
**Related:** T003A

## Objective

Make client-side crop/upload reliable on external devices in the same local network while keeping image processing client-side and ensuring the database stores only file paths.

## Implemented behavior

1. **Feature detect WebP encoding**
   - If WebP encoding is available: output WebP.
   - If WebP encoding is not available: silently fall back to JPEG/PNG.

2. **Crop-first, resize-second**
   - Resizing is applied using crop output dimensions.
   - Resize cap (`2048`) is applied **only when cropped width or height exceeds 2048**.
   - If cropped output is already below 2048, no resize is forced.

3. **Preview reliability**
   - Removed pre-crop conversion stage.
   - Preview now loads directly from selected file data URL.
   - Avoids failed previews caused by strict pre-conversion assumptions.

4. **Server accepts only supported processed image types**
   - Accepted data URIs:
     - `data:image/webp;base64,...`
     - `data:image/png;base64,...`
     - `data:image/jpeg;base64,...`
     - `data:image/jpg;base64,...`
   - Keeps upload constraints explicit and predictable.

5. **Database stores file path only**
   - Processed image is written to storage disk.
   - `photos.path` stores relative file path, never raw base64 payload.

6. **Image URL rendering for external devices**
   - Blade image sources switched from `asset('storage/...')` to `Storage::url(...)`.
   - Prevents host mismatch issues (e.g., `APP_URL=localhost`) when accessed via local-network IP.

## Files changed

- `resources/js/image-cropper.js`
  - Feature detection for WebP encoding
  - Crop-first preview pipeline
  - Conditional resize from cropped dimensions only
  - WebP primary output with JPEG/PNG fallback
- `app/Http/Requests/StorePhotoRequest.php`
  - Validation expanded to WebP/PNG/JPEG/JPG data URIs
- `app/Http/Requests/UpdatePhotoRequest.php`
  - Same format validation alignment
- `app/Http/Controllers/PhotoController.php`
  - Supported image-data check centralized and reused in store/update
- `app/Services/ImageProcessor.php`
  - Stores supported formats with correct extension (`.webp`, `.png`, `.jpg`)
- `resources/views/components/image-cropper.blade.php`
  - Updated UX copy to describe fallback + resize rule
- `resources/views/photos/index.blade.php`
- `resources/views/photos/show.blade.php`
- `resources/views/albums/index.blade.php`
- `resources/views/guestbook/index.blade.php`
- `resources/views/milestones/index.blade.php`
  - Image src now uses `Storage::url(...)` for host-safe rendering
- `tests/Feature/PhotoUploadTest.php`
  - Added assertions for path-only persistence
  - Added PNG and JPEG fallback acceptance tests

## Acceptance checks

- External device on local network can crop/upload without preview breakage.
- WebP is used when available; JPEG/PNG is accepted when WebP encoding is unavailable.
- Resize applies only when cropped output exceeds 2048.
- Uploaded files are saved to storage and DB stores only path strings.
- `public/storage` symlink exists (`php artisan storage:link`) so Storage URLs resolve.
