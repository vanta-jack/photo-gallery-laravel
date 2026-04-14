@extends('layouts.app')

@section('title', 'Edit Guestbook Entry')

@section('content')
@php
$post = $guestbook->post;
$selectedPhotoIds = collect(old('photo_ids', $guestbook->photos->pluck('id')->all()))
    ->map(static fn ($id): int => (int) $id)
    ->filter(static fn (int $id): bool => $id > 0)
    ->values()
    ->all();
$mainPick = old('main_photo_pick', $guestbook->photo_id ? 'existing:'.$guestbook->photo_id : null);
@endphp

<div class="mx-auto max-w-2xl space-y-4 md:max-w-3xl">
    <x-ui.card class="space-y-4" aria-labelledby="guestbook-edit-heading">
        <x-slot:header>
            <div class="space-y-1">
                <h1 id="guestbook-edit-heading" class="text-2xl font-bold text-foreground">Edit guestbook entry</h1>
                <p class="text-sm text-muted-foreground">Update your title, message, or attached photo reference.</p>
            </div>
        </x-slot:header>

        @if($errors->any())
            <x-ui.alert
                variant="destructive"
                title="Could not save changes."
                description="Please check the highlighted fields and submit again."
            />
        @endif

        <form method="POST" action="{{ route('guestbook.update', $guestbook) }}" class="space-y-4" data-photo-base64-form data-photo-required="false">
            @csrf
            @method('PUT')

            <x-ui.form-input
                name="title"
                label="Title"
                required
                maxlength="255"
                autocomplete="off"
                :value="$post?->title"
                placeholder="Entry title"
            />

            <x-ui.markdown-editor
                name="description"
                label="Message"
                :required="true"
                :rows="6"
                :value="$post?->description"
                placeholder="Update your message..."
            />

            <x-ui.photo-attachments
                label="Photos (optional)"
                help="Select or upload photos, then choose the main image shown for this entry."
                :available-photos="$userPhotos ?? collect()"
                :selected-photo-ids="$selectedPhotoIds"
                :main-pick="$mainPick"
                :allow-existing="true"
                :allow-upload="true"
                upload-title="Upload guestbook photos"
            />

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button type="submit">Save changes</x-ui.button>
                <a
                    href="{{ route('guestbook.index') }}"
                    class="text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80"
                >
                    Cancel
                </a>
            </div>
        </form>
    </x-ui.card>
</div>

@endsection
