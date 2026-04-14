@extends('layouts.app')

@section('title', 'Rate Photo')

@section('content')
@php
$photoTitle = trim((string) ($photo->title ?? ''));
if ($photoTitle === '') {
    $photoTitle = 'Untitled photo';
}

$path = trim((string) ($photo->path ?? ''));
$photoUrl = $path === ''
    ? null
    : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
        ? $path
        : \Illuminate\Support\Facades\Storage::url($path));

$selectedRating = old('rating', $existingRating?->rating);
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Rate Photo</h1>
                <p class="text-sm text-muted-foreground">Choose a star rating for <span class="font-bold text-foreground">{{ $photoTitle }}</span>.</p>
            </div>
        </x-slot:header>

        @if($photoUrl)
            <div class="overflow-hidden rounded border border-border bg-secondary">
                <img src="{{ $photoUrl }}" alt="{{ $photoTitle }}" class="max-h-80 w-full object-cover" loading="lazy">
            </div>
        @endif

        @if($existingRating)
            <x-ui.alert
                variant="muted"
                title="You already rated this photo."
                description="Submit a new value below to update your previous rating."
            />
        @endif

        <form action="{{ route('photos.ratings.store', $photo) }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-3">
                <label class="block text-sm font-bold text-foreground">Rating</label>
                <div class="flex gap-2">
                    @for($value = 1; $value <= 5; $value++)
                        <label class="group relative cursor-pointer">
                            <input
                                type="radio"
                                name="rating"
                                value="{{ $value }}"
                                @checked((string) $selectedRating === (string) $value)
                                class="absolute inset-0 opacity-0"
                                required
                            >
                            <div class="flex h-12 w-12 items-center justify-center rounded border-2 transition-all duration-150 @if((string) $selectedRating === (string) $value) border-yellow-500 bg-yellow-50 @else border-border bg-background group-hover:border-yellow-500 group-hover:bg-yellow-50 @endif">
                                <x-icon name="star" class="h-6 w-6 fill-yellow-500 text-yellow-500" />
                            </div>
                            <span class="sr-only">{{ $value }} star{{ $value === 1 ? '' : 's' }}</span>
                        </label>
                    @endfor
                </div>
                <p class="text-xs text-muted-foreground">Click a star to select your rating</p>
                @error('rating')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit">Save rating</x-ui.button>

                <a
                    href="{{ route('photos.show', $photo) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Cancel
                </a>
            </div>
        </form>

        @if($existingRating)
            <form action="{{ route('photos.ratings.destroy', [$photo, $existingRating]) }}" method="POST" class="border-t border-border pt-4">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                    onclick="return confirm('Remove your rating for this photo?')"
                >
                    Remove rating
                </button>
            </form>
        @endif
    </x-ui.card>
</div>
@endsection
