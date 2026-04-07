# T017: Multi-Image Upload

**Status:** IN PROGRESS  
**Tag:** `multi-image-upload`

---

## Problem Statement

The current photo upload interface accepts only single-file uploads, requiring users to repeat the upload process for each photo when adding multiple images to their gallery.

## Specifications

### Feature Requirements
1. **Multiple file selection** — Enable HTML input type="file" with multiple attribute to accept batch photo selection
2. **Preview grid** — Display selected photos in a preview grid before upload, showing thumbnails with individual remove buttons
3. **Batch processing** — Process all selected photos through ImageProcessor service (WebP conversion, metadata strip, compression) in a single request
4. **Progress indication** — Show upload/processing progress (e.g., "Processing 3 of 5...") during batch operation
5. **Error handling** — Display per-file errors for failed uploads while allowing successful uploads to complete
6. **Album association** — Support batch upload with optional album assignment for all photos at once
7. **Brand-compliant UI** — Follow VANITI FAIRE brand kit styling for upload interface

### Technical Requirements
- **Database:** No new migrations. Use existing photos table with user_id and optional album_photo junction.
- **Routes:** Enhance existing photos.store route to accept photos[] array input instead of single photo.
- **Dependencies:** Existing ImageProcessor service from T003. No new packages.
- **Files:**
  - `app/Http/Requests/StorePhotoRequest.php` (update validation to accept photos[] array)
  - `app/Http/Controllers/PhotoController.php` (refactor store method for batch processing)
  - `resources/views/photos/create.blade.php` (add multiple file input with preview grid)
  - `resources/js/photo-upload.js` (preview generation, progress tracking, client-side validation)
  - `tests/Feature/MultiImageUploadTest.php` (feature tests for batch upload logic)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **update-store-photo-request** — Modify StorePhotoRequest validation to accept 'photos.*' array input with individual file validation rules (max size, webp format)

2. **refactor-photo-controller-store** — Update PhotoController::store to loop through photos[] array, process each via ImageProcessor, and create Photo model records in batch (depends: update-store-photo-request)

3. **add-multiple-file-input** — Update photos/create.blade.php with input type="file" multiple attribute and hidden photos[] array handling (depends: refactor-photo-controller-store)

4. **implement-preview-grid** — Build JavaScript module to generate thumbnail previews of selected files in a grid layout with individual remove buttons (depends: add-multiple-file-input)

5. **add-progress-indication** — Display processing progress ("Uploading 3 of 5...") during batch upload and disable submit button until completion (depends: implement-preview-grid)

6. **write-feature-tests** — Test batch upload with multiple photos, error handling for mixed success/failure, and album association (depends: add-progress-indication)

---

## Resolution Summary

Not started. Proposal defines multi-image upload with preview grid and batch processing.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** feature tests for batch processing and error handling
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
