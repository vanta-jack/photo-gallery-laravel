@extends('layouts.app')

@section('title', 'Upload Photo')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6">Upload Photo</h1>

    <form action="{{ route('photos.store') }}" method="POST" data-photo-base64-form>
        @csrf

        <div class="space-y-6">
            <div class="space-y-4">
                <label for="photo_file" class="block text-sm font-bold text-foreground">
                    Photo <span class="text-destructive">*</span>
                </label>

                <input
                    id="photo_file"
                    type="file"
                    accept="image/webp,image/png,image/jpeg"
                    multiple
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring cursor-pointer hover:bg-card transition-colors"
                    data-photo-file-input
                    required
                />

                <input type="hidden" name="photo" value="{{ old('photo') }}" data-photo-base64-input />
                <div data-photo-base64-list></div>

                <p class="hidden text-xs text-muted-foreground" data-photo-upload-status></p>
                <p class="hidden text-destructive text-sm" data-photo-upload-error></p>

                <div class="hidden border border-border rounded p-3 bg-background/30 space-y-3" data-photo-preview-panel>
                    <p class="text-xs text-muted-foreground">
                        Review selected files before upload.
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" data-photo-preview-grid></div>
                </div>

                <p class="text-xs text-muted-foreground">
                    Select one or more WebP, PNG, or JPEG images. Each image is processed in your browser before upload.
                </p>
            </div>

            @error('photo')
                <span class="text-destructive text-sm">{{ $message }}</span>
            @enderror
            @error('photos')
                <span class="text-destructive text-sm">{{ $message }}</span>
            @enderror
            @error('photos.*')
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
                <p class="text-muted-foreground text-sm mt-2">If empty, will use "Photo" as the title.</p>
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
                    data-markdown-editor
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                >{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-destructive text-sm">{{ $message }}</span>
                @enderror
            </div>

            @auth
                <div>
                    <label for="album_id" class="block text-sm font-bold text-foreground mb-2">Album (Optional)</label>
                    <select
                        id="album_id"
                        name="album_id"
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">No album</option>
                        @foreach($albums as $album)
                            <option value="{{ $album->id }}" @selected((string) old('album_id') === (string) $album->id)>
                                {{ $album->title }}
                            </option>
                        @endforeach
                    </select>
                    @if($albums->isEmpty())
                        <p class="text-muted-foreground text-sm mt-2">Create an album first if you want to group this upload.</p>
                    @endif
                    @error('album_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>
            @endauth

            <div class="flex gap-4">
                <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150" data-photo-submit-button>
                    Upload Photo
                </button>
                <a href="{{ route('photos.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
