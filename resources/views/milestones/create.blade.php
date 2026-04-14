@extends('layouts.app')

@section('title', 'Create Milestone')

@section('content')
@php
$selectedStage = old('stage', array_key_first($curatedStages));
$selectedPhotoIds = collect(old('photo_ids', []))
    ->map(static fn ($id): int => (int) $id)
    ->filter(static fn (int $id): bool => $id > 0)
    ->values()
    ->all();
$selectedAlbumId = old('album_id');
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Create Milestone</h1>
                <p class="text-sm text-muted-foreground">Document a life event and optionally attach it to an album.</p>
            </div>
        </x-slot:header>

        <form action="{{ route('milestones.store') }}" method="POST" class="space-y-6" data-photo-base64-form data-photo-required="true">
            @csrf

            <x-ui.form-input
                name="label"
                label="Milestone label"
                :value="old('label')"
                placeholder="First day of preschool"
                required
            />

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 max-w-2xl">
                <x-ui.form-select
                    name="stage"
                    label="Life stage"
                    :value="$selectedStage"
                    required
                >
                    @foreach($curatedStages as $value => $label)
                        <option value="{{ $value }}" @selected((string) $selectedStage === (string) $value)>{{ $label }}</option>
                    @endforeach
                    <option value="custom" @selected((string) $selectedStage === 'custom')>Custom</option>
                </x-ui.form-select>

                <x-ui.form-input
                    name="stage_custom"
                    label="Custom stage label"
                    :value="old('stage_custom')"
                    placeholder="Middle school transition"
                    help="Required only when life stage is set to Custom."
                />
            </div>

            <x-ui.markdown-editor
                name="description"
                label="Description"
                :value="old('description')"
                :rows="6"
                placeholder="Capture what made this moment meaningful..."
            />

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 max-w-2xl">
                <x-ui.form-select
                    name="album_id"
                    label="Album (optional)"
                    :value="$selectedAlbumId"
                    placeholder="No album"
                    help="Attach selected and newly uploaded photos to this album."
                >
                    @foreach($albums as $album)
                        <option value="{{ $album->id }}" @selected((string) $selectedAlbumId === (string) $album->id)>{{ $album->title }}</option>
                    @endforeach
                </x-ui.form-select>
            </div>

            <x-ui.photo-attachments
                label="Milestone photos (required)"
                help="Select existing photos and/or upload new ones. Choose which photo is the main image."
                :available-photos="$userPhotos"
                :selected-photo-ids="$selectedPhotoIds"
                :main-pick="old('main_photo_pick')"
                :allow-existing="true"
                :allow-upload="true"
                :required="true"
                upload-title="Upload milestone photos"
            />

            <label class="flex items-center gap-2 text-sm text-foreground">
                <input type="checkbox" name="is_public" value="1" @checked(old('is_public')) class="h-4 w-4 rounded border-input text-primary focus-visible:ring-ring">
                Make this milestone visible on the public home feed
            </label>

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit" data-photo-submit-button>Create milestone</x-ui.button>
                <x-ui.button as="a" variant="secondary" href="{{ route('milestones.index') }}">Cancel</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
