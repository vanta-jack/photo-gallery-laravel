@extends('layouts.app')

@section('title', 'Upload Photo')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6">Upload Photo</h1>

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
                <label for="title" class="block text-sm font-bold text-foreground mb-2">Title (Optional)</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}"
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                />
                <p class="text-muted-foreground text-sm mt-2">If empty, will use "Cropped Photo" as the title.</p>
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
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                >{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-destructive text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                    Upload Photo
                </button>
                <a href="{{ route('photos.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
