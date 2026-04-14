@extends('layouts.app')

@section('title', 'Write in Guestbook')

@section('content')
@php
$isAuthenticated = auth()->check();
$selectedPhotoIds = collect(old('photo_ids', []))
    ->map(static fn ($id): int => (int) $id)
    ->filter(static fn (int $id): bool => $id > 0)
    ->values()
    ->all();
@endphp
<div class="mx-auto max-w-2xl space-y-4 md:max-w-3xl">
    <x-ui.card class="space-y-4" aria-labelledby="guestbook-create-heading">
        <x-slot:header>
            <div class="space-y-1">
                <h1 id="guestbook-create-heading" class="text-2xl font-bold text-foreground">Write in the guestbook</h1>
                <p class="text-sm text-muted-foreground">
                    Share a short message with visitors. Guests can post anonymously, signed-in users keep attribution.
                </p>
            </div>
        </x-slot:header>

        @if($errors->any())
            <x-ui.alert
                variant="destructive"
                title="Could not publish entry."
                description="Please check the form and try again."
            />
        @endif

        <form method="POST" action="{{ route('guestbook.store') }}" class="space-y-4" data-photo-base64-form data-photo-required="false">
            @csrf

            <x-ui.form-input
                name="title"
                label="Title"
                required
                maxlength="255"
                autocomplete="off"
                placeholder="e.g. Visiting from Berlin"
            />

            <x-ui.markdown-editor
                name="description"
                label="Message"
                :required="true"
                :rows="6"
                placeholder="Share your thoughts about the gallery..."
            />

            <x-ui.photo-attachments
                label="Photos (optional)"
                help="Add one or more photos. Main image defaults to the first selected/uploaded photo, and you can change it."
                :available-photos="$userPhotos ?? collect()"
                :selected-photo-ids="$selectedPhotoIds"
                :main-pick="old('main_photo_pick')"
                :allow-existing="$isAuthenticated"
                :allow-upload="true"
                upload-title="Upload guestbook photos"
            />

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button type="submit">Publish entry</x-ui.button>
                <a
                    href="{{ route('guestbook.index') }}"
                    class="text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80"
                >
                    Back to guestbook feed
                </a>
            </div>
        </form>
    </x-ui.card>
</div>

@endsection
