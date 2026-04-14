@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
@php
$selectedPhotoIds = collect(old('photo_ids', []))
    ->map(static fn ($id): int => (int) $id)
    ->filter(static fn (int $id): bool => $id > 0)
    ->values()
    ->all();
@endphp
<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Create Post</h1>
                <p class="text-sm text-muted-foreground">Share an update with markdown formatting support.</p>
            </div>
        </x-slot:header>

        <form action="{{ route('posts.store') }}" method="POST" class="space-y-6" data-photo-base64-form data-photo-required="false">
            @csrf

            <x-ui.form-input
                name="title"
                label="Title"
                :value="old('title')"
                placeholder="Post title"
                required
            />

            <x-ui.markdown-editor
                name="description"
                label="Content"
                :value="old('description')"
                :rows="12"
                placeholder="Write your post in markdown..."
                help="Use markdown for headings, lists, links, and emphasis."
                :required="true"
            />

            <x-ui.photo-attachments
                label="Post photos (optional)"
                help="Add one or more photos and choose the main image displayed with this post."
                :available-photos="$userPhotos ?? collect()"
                :selected-photo-ids="$selectedPhotoIds"
                :main-pick="old('main_photo_pick')"
                :allow-existing="true"
                :allow-upload="true"
                upload-title="Upload post photos"
            />

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit">Publish post</x-ui.button>
                <x-ui.button as="a" variant="secondary" href="{{ route('posts.index') }}">Cancel</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
