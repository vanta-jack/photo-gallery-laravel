@extends('layouts.app')

@section('title', 'Edit Milestone')

@section('content')
@php
    $selectedPhotoId = old('photo_id', $milestone->photo_id);
    $selectedAlbumId = old('album_id');
    $selectedStage = old('stage');
    $milestoneStage = $milestone->stage;
    $isMilestoneStageCurated = array_key_exists($milestoneStage, $curatedStages);
    $selectedStage = $selectedStage ?? ($isMilestoneStageCurated ? $milestoneStage : 'custom');
    $customStageValue = old('stage_custom', $isMilestoneStageCurated ? '' : $milestoneStage);
@endphp
<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6">Edit Milestone</h1>

    <form action="{{ route('milestones.update', $milestone) }}" method="POST" data-photo-base64-form data-photo-required="false">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label for="stage" class="block text-sm font-bold mb-2 text-foreground">Life Stage</label>
                <select id="stage" name="stage" required class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Select stage...</option>
                    @foreach($curatedStages as $stageValue => $stageLabel)
                        <option value="{{ $stageValue }}" @selected($selectedStage === $stageValue)>{{ $stageLabel }}</option>
                    @endforeach
                    <option value="custom" @selected($selectedStage === 'custom')>Custom stage…</option>
                </select>
                @error('stage')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="stage_custom" class="block text-sm font-bold mb-2 text-foreground">Custom Stage (when "Custom stage…" is selected)</label>
                <input
                    type="text"
                    id="stage_custom"
                    name="stage_custom"
                    value="{{ $customStageValue }}"
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                    placeholder="Examples: Gap Year, Residency, Retirement"
                >
                @error('stage_custom')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="label" class="block text-sm font-bold mb-2 text-foreground">Label (e.g., "Month 3", "Grade 2")</label>
                <input type="text" id="label" name="label" value="{{ old('label', $milestone->label) }}" required class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">
                @error('label')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-bold mb-2 text-foreground">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    data-markdown-editor
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                >{{ old('description', $milestone->description) }}</textarea>
                @error('description')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            <label class="flex items-center gap-2 text-sm font-bold text-foreground">
                <input
                    type="hidden"
                    name="is_public"
                    value="0"
                >
                <input
                    type="checkbox"
                    name="is_public"
                    value="1"
                    class="rounded border-input"
                    @checked(old('is_public', $milestone->is_public))
                >
                Show this milestone publicly on Home
            </label>
            @error('is_public')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror

            <div class="space-y-4 border-t border-border pt-6">
                <div>
                    <h2 class="text-lg font-bold text-foreground mb-1">Milestone Photo</h2>
                    <p class="text-sm text-muted-foreground">Update or replace the milestone photo as needed.</p>
                </div>

                @if($milestone->photo)
                    <div class="flex items-center gap-4 rounded border border-border p-3 bg-background/40">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($milestone->photo->path) }}" alt="Current milestone photo" class="w-20 h-20 object-cover rounded">
                        <div>
                            <p class="text-sm font-bold text-foreground">Current Photo</p>
                            <p class="text-xs text-muted-foreground">{{ $milestone->photo->title ?: 'Untitled' }}</p>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="photo_id" class="block text-sm font-bold mb-2 text-foreground">Use Existing Photo</label>
                    <select
                        id="photo_id"
                        name="photo_id"
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Keep current photo</option>
                        @foreach($userPhotos as $photo)
                            <option value="{{ $photo->id }}" @selected((string) $selectedPhotoId === (string) $photo->id)>
                                {{ $photo->title ?: 'Photo #'.$photo->id }}
                            </option>
                        @endforeach
                    </select>
                    @if($userPhotos->isEmpty())
                        <p class="text-muted-foreground text-sm mt-2">You do not have any existing photos yet.</p>
                    @endif
                    @error('photo_id')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
                </div>

                <div class="space-y-4">
                    <label for="milestone_photo_file" class="block text-sm font-bold text-foreground">
                        Upload New Photo(s)
                    </label>

                    <input
                        id="milestone_photo_file"
                        type="file"
                        accept="image/webp,image/png,image/jpeg"
                        multiple
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring cursor-pointer hover:bg-card transition-colors"
                        data-photo-file-input
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
                        Upload one or more WebP, PNG, or JPEG images. The first uploaded photo will be used for the milestone.
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
            </div>

            <div>
                <label for="album_id" class="block text-sm font-bold text-foreground mb-2">Album (Optional)</label>
                <select
                    id="album_id"
                    name="album_id"
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">No album</option>
                    @foreach($albums as $album)
                        <option value="{{ $album->id }}" @selected((string) $selectedAlbumId === (string) $album->id)>
                            {{ $album->title }}
                        </option>
                    @endforeach
                </select>
                @if($albums->isEmpty())
                    <p class="text-muted-foreground text-sm mt-2">Create an album first if you want to group these photos.</p>
                @endif
                @error('album_id')
                    <span class="text-destructive text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150" data-photo-submit-button>
                    Update Milestone
                </button>
                <a href="{{ route('milestones.show', $milestone) }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
