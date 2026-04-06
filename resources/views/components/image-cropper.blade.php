@props([
    'name',
    'label' => 'Select image',
    'required' => false
])

<div 
    data-image-uploader
    class="space-y-4"
>
    <label class="block text-sm font-bold text-foreground">
        {{ $label }}
        @if($required)<span class="text-destructive">*</span>@endif
    </label>
    
    <input 
        type="file" 
        accept="image/*" 
        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring cursor-pointer hover:bg-card transition-colors"
        @if($required) required @endif
    />
    
    <input type="hidden" name="{{ $name }}" data-image-result />
    
    <div class="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded p-3 space-y-2">
        <p class="text-xs font-semibold text-blue-900 dark:text-blue-100">
            📸 Image Processing Pipeline
        </p>
        <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-1 ml-4 list-disc">
            <li>Select an image file</li>
            <li>Image is processed on your device before upload</li>
            <li>Converts to WebP when supported for smaller file sizes</li>
            <li>Falls back to original format (JPEG/PNG) if WebP not available</li>
            <li>Click "Upload Photo" to submit</li>
        </ul>
    </div>
    
    <p class="text-xs text-muted-foreground">
        💡 All image processing happens on your device. The database stores only file paths to uploaded images.
    </p>
</div>
