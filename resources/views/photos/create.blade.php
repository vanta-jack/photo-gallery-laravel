@extends('layouts.app')

@section('title', 'Upload Photo')

@section('content')
@php
$isAuthenticated = auth()->check();
$selectedAlbum = old('album_id');
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Upload Photo</h1>
                <p class="text-sm text-muted-foreground">Select one or more images, add metadata, then submit to your gallery.</p>
            </div>
        </x-slot:header>

        <form action="{{ route('photos.store') }}" method="POST" class="space-y-6" data-photo-base64-form>
            @csrf

            <div class="space-y-2">
                <label for="photo-files" class="block text-sm font-bold text-foreground">
                    Image files
                    <span class="text-destructive" aria-hidden="true">*</span>
                </label>

                <input
                    id="photo-files"
                    type="file"
                    accept="image/webp,image/png,image/jpeg"
                    multiple
                    data-photo-file-input
                    class="block w-full rounded border border-input bg-background px-3 py-2 text-sm text-foreground file:mr-3 file:rounded file:border file:border-border file:bg-secondary file:px-3 file:py-1 file:text-xs file:font-bold file:text-secondary-foreground"
                >

                <p class="text-xs text-muted-foreground">Supported formats: WebP, PNG, JPEG. You can select multiple files at once.</p>

                @error('photo')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
                @error('photos')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
                @error('photos.*')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <input type="hidden" name="photo" value="" data-photo-base64-input>
            <div data-photo-base64-list></div>

            <p data-photo-upload-status class="hidden rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground"></p>
            <p data-photo-upload-error class="hidden rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive"></p>

            <section data-photo-preview-panel class="hidden space-y-3 rounded border border-border bg-card p-4">
                <h2 class="text-sm font-bold text-foreground">Selected images</h2>
                <div data-photo-preview-grid class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3"></div>
            </section>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 max-w-2xl">
                <x-ui.form-input
                    name="title"
                    label="Title"
                    :value="old('title')"
                    placeholder="Evening skyline"
                    help="Optional. If omitted, uploads are titled “Photo”."
                />

                @if($isAuthenticated)
                    <x-ui.form-select
                        name="album_id"
                        label="Album"
                        :value="$selectedAlbum"
                        placeholder="No album"
                        help="Optional. You can also organize photos later."
                    >
                        @foreach($albums as $album)
                            <option value="{{ $album->id }}" @selected((string) $selectedAlbum === (string) $album->id)>
                                {{ $album->title }}
                            </option>
                        @endforeach
                    </x-ui.form-select>
                @else
                    <x-ui.card padding="sm" class="space-y-2">
                        <p class="text-sm font-bold text-foreground">Guest upload mode</p>
                        <p class="text-sm text-muted-foreground">Sign in if you want to attach uploads directly to one of your albums.</p>
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                        >
                            Log in
                        </a>
                    </x-ui.card>
                @endif
            </div>

            <x-ui.markdown-editor
                name="description"
                label="Description"
                :value="old('description')"
                :rows="4"
                placeholder="Describe the moment, location, or context."
            />

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit" data-photo-submit-button>
                    Upload photo
                </x-ui.button>

                <a
                    href="{{ $isAuthenticated ? route('photos.index') : route('home') }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Cancel
                </a>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
