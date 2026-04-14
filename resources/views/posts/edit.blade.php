@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
@php
$selectedPhotoIds = collect(old('photo_ids', $post->photos->pluck('id')->push($post->photo_id)->all()))
    ->map(static fn ($id): int => (int) $id)
    ->filter(static fn (int $id): bool => $id > 0)
    ->values()
    ->all();
$mainPick = old('main_photo_pick', $post->photo_id ? 'existing:'.$post->photo_id : null);
@endphp
<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Edit Post</h1>
                <p class="text-sm text-muted-foreground">Update your post content and keep markdown formatting intact.</p>
            </div>
        </x-slot:header>

        <form action="{{ route('posts.update', $post) }}" method="POST" class="space-y-6" data-photo-base64-form data-photo-required="false">
            @csrf
            @method('PUT')

            <x-ui.form-input
                name="title"
                label="Title"
                :value="old('title', $post->title)"
                placeholder="Post title"
                required
            />

            <x-ui.markdown-editor
                name="description"
                label="Content"
                :value="old('description', $post->description)"
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
                :main-pick="$mainPick"
                :allow-existing="true"
                :allow-upload="true"
                upload-title="Upload post photos"
            />

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit">Save changes</x-ui.button>
                <x-ui.button as="a" variant="secondary" href="{{ route('posts.show', $post) }}">Cancel</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
