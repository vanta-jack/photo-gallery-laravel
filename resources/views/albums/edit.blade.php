@extends('layouts.app')

@section('title', 'Edit Album')

@section('content')
@php
$selectedPhotoIds = collect(old('photo_ids', $album->photos->pluck('id')->all()))
    ->map(static fn ($id): string => (string) $id)
    ->all();

$mainPick = old('main_photo_pick', $album->cover_photo_id ? 'existing:'.$album->cover_photo_id : null);

$allPhotos = $album->photos
    ->concat($userPhotos)
    ->unique('id')
    ->sortByDesc('id')
    ->values();
$legacyCoverId = old('cover_photo_id', $album->cover_photo_id);

@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Edit Album</h1>
                <p class="text-sm text-muted-foreground">Update details, privacy, and the set of photos in this album.</p>
            </div>
        </x-slot:header>

        <form action="{{ route('albums.update', $album) }}" method="POST" class="space-y-6" data-photo-base64-form data-photo-required="false">
            @csrf
            @method('PUT')

            <x-ui.form-input
                name="title"
                label="Album title"
                :value="old('title', $album->title)"
                placeholder="Album title"
                required
            />

            <x-ui.markdown-editor
                name="description"
                label="Description"
                :value="old('description', $album->description)"
                :rows="4"
                placeholder="Explain the theme of this album."
            />

            <label class="inline-flex items-center gap-2 text-sm font-bold text-foreground">
                <input
                    type="checkbox"
                    name="is_private"
                    value="1"
                    @checked((bool) old('is_private', $album->is_private))
                    class="h-4 w-4 rounded border border-input bg-background text-primary focus-visible:ring-2 focus-visible:ring-ring"
                >
                Keep this album private
            </label>

            <x-ui.photo-attachments
                label="Cover photo & album photos (optional)"
                help="Select existing photos and/or upload new ones, then choose the cover photo from the selected set."
                :available-photos="$allPhotos"
                :selected-photo-ids="$selectedPhotoIds"
                :main-pick="$mainPick"
                legacy-main-id-name="cover_photo_id"
                :legacy-main-id="$legacyCoverId"
                :allow-existing="true"
                :allow-upload="true"
                upload-title="Upload album photos"
            />

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit">Save album</x-ui.button>

                <a
                    href="{{ route('albums.show', $album) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Cancel
                </a>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card padding="sm" class="space-y-3">
        <h2 class="text-sm font-bold text-foreground">Danger zone</h2>
        <p class="text-sm text-muted-foreground">Deleting this album removes the album record. Photos remain available unless explicitly removed elsewhere.</p>

        <form action="{{ route('albums.destroy', $album) }}" method="POST">
            @csrf
            @method('DELETE')
            <button
                type="submit"
                class="inline-flex items-center rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                onclick="return confirm('Delete this album?')"
            >
                Delete album
            </button>
        </form>
    </x-ui.card>
</div>
@endsection
