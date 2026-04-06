# T002: Image Upload with Cropping (Frontend)

**Status:** ✅ IMPLEMENTED - Client-Side Processing Complete  
**Implemented:** 2026-04-06  
**Implementation Notes:**
- Installed cropperjs v2.1.0 and browser-image-compression v2.0.2 using bun
- **All image processing happens on client-side:**
  - User selects image → automatic compression/resizing → cropper appears
  - Max 2048x2048 dimensions enforced before cropping
  - WebP conversion at 85% quality
  - Metadata automatically stripped during conversion
  - Final cropped result converted to WebP base64
- Created `resources/js/image-cropper.js` with integrated compression
- Created `resources/views/components/image-cropper.blade.php` Blade component
- Updated `PhotoController` to receive and store processed WebP data
- Updated `StorePhotoRequest` and `UpdatePhotoRequest` for base64 validation
- Updated `resources/views/photos/create.blade.php` to use image-cropper component
- Backend simplified to just store already-processed WebP files
- Frontend assets built successfully with Vite
- No server-side dependencies required (removed intervention/image)

**Priority:** High  
**Type:** Feature  
**Estimated Effort:** Medium

## Summary

Add client-side image cropping to photo uploads using Cropper.js. The cropped image is sent to the backend as base64 data; the backend never receives the original uncropped image.

## Current State

- `resources/views/photos/create.blade.php` has a simple file input
- No JavaScript for image manipulation exists
- `resources/js/app.js` is minimal
- Users upload images as-is without any client-side processing

## Requirements

From `.tickets/active/004-site-implementations.md`:
> TODO: An upload system must include image cropping, everything happens on the frontend and the backend only receives the cropped image--use bun's cropper.js.

## Implementation Steps

### 1. Install dependencies

```bash
bun add cropperjs browser-image-compression
```

### 2. Create client-side cropper + compressor

- Use `browser-image-compression` to resize to max 2048px and convert to WebP
- Render the compressed image inside `cropperjs` (web component)
- Export the cropped result as **WebP base64** on submit

### 3. Wire frontend entrypoint

- Import `resources/js/image-cropper.js` in `resources/js/app.js`

### 4. Create Blade component

- `resources/views/components/image-cropper.blade.php`
- Uses `data-cropper-container` for the web component

### 5. Update photo upload view

- Replace file input with `<x-image-cropper>`
- Remove multipart form data (base64 is sent via hidden input)

### 6. Update backend to expect WebP base64

- `PhotoController::store()` stores only WebP base64 input
- `StorePhotoRequest` and `UpdatePhotoRequest` validate WebP base64 strings

## Files to Create/Modify

| File | Action |
|------|--------|
| `package.json` | Modify (add cropperjs + browser-image-compression) |
| `resources/js/image-cropper.js` | Create/Modify |
| `resources/js/app.js` | Modify (add import) |
| `resources/views/components/image-cropper.blade.php` | Create |
| `resources/views/photos/create.blade.php` | Modify |
| `app/Http/Controllers/PhotoController.php` | Modify |
| `app/Http/Requests/StorePhotoRequest.php` | Modify |
| `app/Http/Requests/UpdatePhotoRequest.php` | Modify |

## Acceptance Criteria

- [ ] User can select an image file
- [ ] Preview shows after selection with cropping UI
- [ ] Client compresses + resizes before cropping (max 2048px)
- [ ] Cropped result is sent as WebP base64
- [ ] Works on photo upload page
- [ ] Backend only stores the processed WebP output

## Dependencies

- T003 (Image Processor) - PhotoController needs ImageProcessor service

## Notes

- Cropper.js is a mature, well-maintained library
- Using JPEG output at 90% quality balances file size and visual quality
- Max canvas dimensions (2048x2048) prevent memory issues on mobile
- The hidden input stores base64 data which is then processed by ImageProcessor
