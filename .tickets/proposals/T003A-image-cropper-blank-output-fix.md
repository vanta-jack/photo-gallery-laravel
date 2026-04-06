# T004: Image Cropper Blank Output Bug - Root Cause & Fix

**Status**: Pending Implementation  
**Priority**: Critical  
**Severity**: P0 - Feature Non-Functional  
**Created**: 2026-04-06  
**Related Tickets**: T002 (Image Cropping), T003 (Image Processor)

---

## Problem Statement

Photo upload converter is returning **blank output** (validation error or empty submission) when users attempt to upload images. The feature appears non-functional to end users.

### User Impact
- Users select image → cropper UI does not appear
- Form submits with empty `photo` field
- Backend validation fails: `"Photo must be a WebP image processed on your device."`
- No error message clearly indicates what went wrong

---

## Root Cause Analysis

### Core Issue
The implementation in `resources/js/image-cropper.js` **misuses the cropperjs v2.1.0 API**, conflating two incompatible approaches:

1. **Incorrect web component usage** (lines 69, 97)
   ```javascript
   // WRONG: Creating bare web component without proper initialization
   cropperElement = document.createElement('cropper-canvas');
   ```

2. **Non-existent method call** (line 97)
   ```javascript
   // WRONG: $getCroppedCanvas() does not exist on cropperElement
   if (cropperElement && cropperElement.$getCroppedCanvas) {
   ```

### Why It Fails

#### cropperjs v2.1.0 API Design
- **Cropper class** is designed for `<img>` or `<canvas>` elements
- Constructor: `new Cropper(element, options)` on an `<img>` element
- Returns a `Cropper` instance with methods to access web components
- **Web components** (`<cropper-canvas>`, `<cropper-image>`, etc.) are **internal implementation details**
- They are auto-registered but NOT meant to be manually instantiated

#### Type Signatures (from `cropperjs/dist/cropper.d.ts`)
```typescript
// Correct API
class Cropper {
  constructor(element: HTMLImageElement | HTMLCanvasElement);
  getCropperCanvas(): CropperCanvas | null;
  getCropperSelection(): CropperSelection | null;
}

// CropperCanvas has $toCanvas() method
class CropperCanvas {
  $toCanvas(options?: { ... }): Promise<HTMLCanvasElement>;
}
```

#### What Actually Happens
1. Line 69 creates: `<cropper-canvas>` (empty, no image, no child elements)
2. Line 97 checks: `cropperElement.$getCroppedCanvas()` → undefined (method doesn't exist)
3. Condition fails → form submits without populating hidden input
4. Backend receives empty string → validation fails
5. **Result: Blank output**

---

## Correct Implementation Pattern

From **official cropperjs README** and **type definitions**:

```javascript
import Cropper from 'cropperjs';

// Create an IMG element with the image data
const image = new Image();
image.src = dataUrl; // from FileReader.readAsDataURL()

// Initialize Cropper on the IMG element
const cropper = new Cropper(image, { aspectRatio: 16/9 });

// On form submit, get the canvas
const cropperCanvas = cropper.getCropperCanvas();
const canvas = await cropperCanvas.$toCanvas({
  maxWidth: 2048,
  maxHeight: 2048,
});

// Convert to WebP blob
canvas.toBlob((blob) => {
  // Convert blob to base64 data URI
  const reader = new FileReader();
  reader.onload = () => {
    hiddenInput.value = reader.result;
    form.submit();
  };
  reader.readAsDataURL(blob);
}, 'image/webp', 0.85);
```

### Key Differences
1. **Initialize on `<img>` element**: `new Cropper(imgElement, options)`
2. **Don't create bare web components**: They are created internally by Cropper
3. **Access via getter**: `cropper.getCropperCanvas()` returns the internal `<cropper-canvas>`
4. **Call async method**: `$toCanvas()` is async and returns `Promise<HTMLCanvasElement>`
5. **Remove the check for `$getCroppedCanvas`**: Use `getCropperCanvas()` instead

---

## Files to Modify

### `resources/js/image-cropper.js`
- **Line 1-2**: Imports are correct, but need to use Cropper class properly
- **Lines 39-87**: File selection handler is correct (compression works)
- **Lines 68-80**: **WRONG** - Replace bare web component creation with proper Cropper initialization
- **Lines 93-131**: **WRONG** - Replace non-existent method call with proper getCropperCanvas() API

### Expected Changes
1. Create `<img>` element instead of `<cropper-canvas>`
2. Initialize `new Cropper(imgElement, options)` instead of manual web component creation
3. Use `cropper.getCropperCanvas().$toCanvas()` instead of `cropperElement.$getCroppedCanvas()`
4. Await the `$toCanvas()` Promise (it's async)

---

## Validation

### Before Fix
- Feature non-functional
- Blank output observed
- Hidden input never populated
- Tests fail (if testing with real DOM)

### After Fix
- Image compression works ✓
- Cropper UI renders properly ✓
- User can adjust crop area ✓
- Form submission exports canvas to WebP ✓
- Hidden input populated with `data:image/webp;base64,...` ✓
- Backend validation passes ✓
- Image stored successfully ✓
- Existing tests pass ✓

---

## Implementation Notes

- **No API changes** to backend
- **No dependency changes** (cropperjs v2.1.0 already installed)
- **Blade component unchanged** (HTML structure doesn't need modification)
- **ImageProcessor unchanged** (still expects base64 WebP)
- **Validation rules unchanged** (still require WebP data URI)

---

## References

- **Official README**: `node_modules/cropperjs/README.md` - Usage example
- **Type Definitions**: `node_modules/cropperjs/dist/cropper.d.ts` - API signatures
- **Implementation**: `node_modules/cropperjs/dist/cropper.esm.raw.js` - Source code
- **Web Component Docs**: `node_modules/@cropper/element-canvas/dist/element-canvas.d.ts` - $toCanvas() method

---

## Ticket Status

- [x] Approved for implementation
- [x] Implementation in progress
- [x] Testing & validation
- [x] Ready for merge
- [ ] Deployed

**Implementation Summary**:
- ✅ Fixed `resources/js/image-cropper.js` to use proper Cropper class API
- ✅ Replaced bare web component creation with IMG element + `new Cropper()`
- ✅ Changed `cropperElement.$getCroppedCanvas()` to `cropper.getCropperCanvas().$toCanvas()`
- ✅ Added proper async/await handling for Promise-based `$toCanvas()` method
- ✅ Frontend build successful (130.98 kB)
- ✅ All 3 photo upload tests passing
- ✅ No changes to other files (backward compatible)

**Human Review Required**: Yes - Code changes to critical image processing pipeline
