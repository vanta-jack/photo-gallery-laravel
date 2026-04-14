@extends('layouts.app')

@section('title', 'Create Album')

@section('content')
@php
$selectedPhotoIds = collect(old('photo_ids', []))
    ->map(static fn ($id): string => (string) $id)
    ->all();
$mainPick = old('main_photo_pick');
$legacyCoverId = old('cover_photo_id');
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Create Album</h1>
                <p class="text-sm text-muted-foreground">Build a collection from your existing uploads and choose an optional cover photo.</p>
            </div>
        </x-slot:header>

        <form action="{{ route('albums.store') }}" method="POST" class="space-y-6" data-photo-base64-form data-photo-required="false">
            @csrf

            <x-ui.form-input
                name="title"
                label="Album title"
                :value="old('title')"
                placeholder="Summer Collection"
                required
            />

            <x-ui.markdown-editor
                name="description"
                label="Description"
                :value="old('description')"
                :rows="4"
                placeholder="Describe what ties these photos together."
            />

            <label class="inline-flex items-center gap-2 text-sm font-bold text-foreground">
                <input
                    type="checkbox"
                    name="is_private"
                    value="1"
                    @checked((bool) old('is_private'))
                    class="h-4 w-4 rounded border border-input bg-background text-primary focus-visible:ring-2 focus-visible:ring-ring"
                >
                Make this album private
            </label>

            <x-ui.photo-attachments
                label="Cover photo & album photos (optional)"
                help="Select existing photos and/or upload new ones, then choose the cover photo from the selected set."
                :available-photos="$userPhotos"
                :selected-photo-ids="$selectedPhotoIds"
                :main-pick="$mainPick"
                legacy-main-id-name="cover_photo_id"
                :legacy-main-id="$legacyCoverId"
                :allow-existing="true"
                :allow-upload="true"
                upload-title="Upload album photos"
            />

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit">Create album</x-ui.button>

                <a
                    href="{{ route('albums.index') }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Cancel
                </a>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
