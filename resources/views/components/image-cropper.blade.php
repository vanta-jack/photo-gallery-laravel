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
    
    <div data-cropper-container class="hidden border border-border rounded overflow-hidden" style="min-height: 300px;">
    </div>
    
    <input type="hidden" name="{{ $name }}" data-cropper-result />
    
    <p class="text-xs text-muted-foreground">
        Select an image, then adjust the crop area before submitting.
    </p>
</div>
