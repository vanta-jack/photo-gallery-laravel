@extends('layouts.app')

@section('title', 'Edit Photo')

@section('content')
@php
$rawPath = trim((string) ($photo->path ?? ''));
$currentUrl = $rawPath === ''
    ? null
    : (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://') || str_starts_with($rawPath, '/')
        ? $rawPath
        : \Illuminate\Support\Facades\Storage::url($rawPath));
@endphp

<div class="mx-auto max-w-3xl space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Edit Photo</h1>
                <p class="text-sm text-muted-foreground">Update metadata or replace the image while keeping the same photo record.</p>
            </div>
        </x-slot:header>

        @if($currentUrl)
            <div class="overflow-hidden rounded border border-border bg-secondary">
                <img src="{{ $currentUrl }}" alt="{{ $photo->title ?: 'Current photo preview' }}" class="max-h-[28rem] w-full object-contain" loading="lazy">
            </div>
        @endif

        <form
            action="{{ route('photos.update', $photo) }}"
            method="POST"
            class="space-y-6"
            data-photo-base64-form
            data-photo-required="false"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-2 lg:col-span-1">
                    <label for="replacement-file" class="block text-sm font-bold text-foreground">Replace image (optional)</label>
                    <input
                        id="replacement-file"
                        type="file"
                        accept="image/webp,image/png,image/jpeg"
                        data-photo-file-input
                        class="block w-full rounded border border-input bg-background px-3 py-2 text-sm text-foreground file:mr-3 file:rounded file:border file:border-border file:bg-secondary file:px-3 file:py-1 file:text-xs file:font-bold file:text-secondary-foreground"
                    >
                    <p class="text-xs text-muted-foreground">Leave empty to keep the current file.</p>

                    @error('photo')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-2">
                    <x-ui.form-input
                        name="title"
                        label="Title"
                        :value="old('title', $photo->title)"
                        placeholder="Photo title"
                    />
                </div>
            </div>

            <input type="hidden" name="photo" value="" data-photo-base64-input>
            <div data-photo-base64-list></div>

            <p data-photo-upload-status class="hidden rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground"></p>
            <p data-photo-upload-error class="hidden rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive"></p>

            <section data-photo-preview-panel class="hidden space-y-3 rounded border border-border bg-card p-4">
                <h2 class="text-sm font-bold text-foreground">Replacement preview</h2>
                <div data-photo-preview-grid class="grid grid-cols-1 gap-3 sm:grid-cols-2"></div>
            </section>

            <x-ui.markdown-editor
                name="description"
                label="Description"
                :value="old('description', $photo->description)"
                :rows="4"
                placeholder="Update the context for this photo."
            />

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit" data-photo-submit-button>
                    Save changes
                </x-ui.button>

                <a
                    href="{{ route('photos.show', $photo) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Cancel
                </a>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
