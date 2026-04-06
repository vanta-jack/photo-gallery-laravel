# T002: Image Upload with Cropping (Frontend)

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

### 1. Install Cropper.js

```bash
bun add cropperjs
```

### 2. Create `resources/js/image-cropper.js`

```javascript
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

/**
 * Initialize image cropper on elements with [data-cropper] attribute.
 * 
 * Usage in Blade:
 * <div data-cropper data-aspect-ratio="1">
 *   <input type="file" accept="image/*" />
 *   <img data-cropper-preview class="hidden" />
 *   <input type="hidden" name="photo" data-cropper-result />
 * </div>
 */
document.addEventListener('DOMContentLoaded', () => {
  const cropperContainers = document.querySelectorAll('[data-cropper]');
  
  cropperContainers.forEach(container => {
    const fileInput = container.querySelector('input[type="file"]');
    const preview = container.querySelector('[data-cropper-preview]');
    const hiddenInput = container.querySelector('[data-cropper-result]');
    const aspectRatio = container.dataset.aspectRatio 
      ? parseFloat(container.dataset.aspectRatio) 
      : NaN; // NaN = free aspect ratio
    
    let cropper = null;
    
    if (!fileInput || !preview || !hiddenInput) {
      console.warn('Cropper container missing required elements');
      return;
    }
    
    // When user selects a file, show preview and initialize cropper
    fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      
      // Validate it's an image
      if (!file.type.startsWith('image/')) {
        alert('Please select an image file.');
        return;
      }
      
      const reader = new FileReader();
      reader.onload = (event) => {
        preview.src = event.target.result;
        preview.classList.remove('hidden');
        
        // Destroy existing cropper if any
        if (cropper) {
          cropper.destroy();
        }
        
        // Initialize new cropper
        cropper = new Cropper(preview, {
          aspectRatio: aspectRatio,
          viewMode: 1,
          autoCropArea: 1,
          responsive: true,
          background: false,
        });
      };
      reader.readAsDataURL(file);
    });
    
    // On form submit, get cropped canvas and set as hidden input value
    const form = container.closest('form');
    if (form) {
      form.addEventListener('submit', (e) => {
        if (cropper) {
          const canvas = cropper.getCroppedCanvas({
            maxWidth: 2048,
            maxHeight: 2048,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
          });
          
          if (canvas) {
            // Convert to base64 JPEG (smaller than PNG)
            hiddenInput.value = canvas.toDataURL('image/jpeg', 0.9);
          }
        }
      });
    }
  });
});
```

### 3. Import in `resources/js/app.js`

```javascript
import './bootstrap';
import './image-cropper.js';
```

### 4. Create Blade Component `resources/views/components/image-cropper.blade.php`

```blade
@props([
    'name',
    'aspectRatio' => null,
    'label' => 'Select image',
    'required' => false
])

<div 
    data-cropper 
    @if($aspectRatio) data-aspect-ratio="{{ $aspectRatio }}" @endif 
    class="space-y-4"
>
    <label class="block text-sm font-bold text-foreground">
        {{ $label }}
        @if($required)<span class="text-destructive">*</span>@endif
    </label>
    
    <input 
        type="file" 
        accept="image/*" 
        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
        @if($required) required @endif
    />
    
    <div class="border border-border rounded overflow-hidden">
        <img 
            data-cropper-preview 
            class="hidden max-w-full" 
            alt="Preview"
        />
    </div>
    
    <input type="hidden" name="{{ $name }}" data-cropper-result />
    
    <p class="text-xs text-muted-foreground">
        Select an image, then adjust the crop area before submitting.
    </p>
</div>
```

### 5. Update `resources/views/photos/create.blade.php`

Replace the file input section:

```blade
@extends('layouts.app')

@section('title', 'Upload Photo')

@section('content')
<div class="bg-card border border-border rounded p-6">
    <h1 class="text-2xl font-bold mb-6">Upload Photo</h1>

    <form action="{{ route('photos.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <x-image-cropper 
                name="photo" 
                label="Photo" 
                :required="true"
            />
            @error('photo')
                <span class="text-destructive text-sm">{{ $message }}</span>
            @enderror

            <div>
                <label for="title" class="block text-sm font-bold text-foreground mb-2">Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}"
                    required
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                />
                @error('title')
                    <span class="text-destructive text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-bold text-foreground mb-2">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                >{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-destructive text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                    Upload
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
```

### 6. Update `resources/views/albums/create.blade.php`

Add cropper for cover photo with square aspect ratio:

```blade
<x-image-cropper 
    name="cover_photo" 
    label="Cover Photo" 
    aspect-ratio="1"
/>
```

### 7. Update `resources/views/users/edit.blade.php`

Add cropper for profile photo with square aspect ratio:

```blade
<x-image-cropper 
    name="profile_photo" 
    label="Profile Photo" 
    aspect-ratio="1"
/>
```

### 8. Update `App\Http\Controllers\PhotoController::store()`

Handle base64 input from cropper:

```php
public function store(StorePhotoRequest $request, ImageProcessor $imageProcessor): RedirectResponse
{
    // Get input - either base64 from cropper or file upload
    $photoInput = $request->input('photo');
    
    // If it's base64 data, process it
    if (is_string($photoInput) && str_starts_with($photoInput, 'data:image')) {
        $path = $imageProcessor->process($photoInput);
    } elseif ($request->hasFile('photo')) {
        // Fallback for regular file upload (if cropper JS didn't load)
        $path = $imageProcessor->process($request->file('photo'));
    } else {
        return back()->withErrors(['photo' => 'An image is required.']);
    }

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
```

### 9. Update `App\Http\Requests\StorePhotoRequest.php`

Allow string input for base64:

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
            'photo' => ['required'], // Can be base64 string or file
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
```

## Files to Create/Modify

| File | Action |
|------|--------|
| `package.json` | Modify (add cropperjs) |
| `resources/js/image-cropper.js` | Create |
| `resources/js/app.js` | Modify (add import) |
| `resources/views/components/image-cropper.blade.php` | Create |
| `resources/views/photos/create.blade.php` | Modify |
| `resources/views/albums/create.blade.php` | Modify |
| `resources/views/users/edit.blade.php` | Modify |
| `app/Http/Controllers/PhotoController.php` | Modify |
| `app/Http/Requests/StorePhotoRequest.php` | Modify |

## Acceptance Criteria

- [ ] User can select an image file
- [ ] Preview shows after selection with cropping UI
- [ ] User can adjust crop area before submission
- [ ] Cropped result (not original) is sent to server
- [ ] Works on photo upload page
- [ ] Works on album create page (cover photo)
- [ ] Works on profile edit page (profile photo)
- [ ] Graceful fallback if JavaScript disabled (regular file upload)

## Dependencies

- T003 (Image Processor) - PhotoController needs ImageProcessor service

## Notes

- Cropper.js is a mature, well-maintained library
- Using JPEG output at 90% quality balances file size and visual quality
- Max canvas dimensions (2048x2048) prevent memory issues on mobile
- The hidden input stores base64 data which is then processed by ImageProcessor
