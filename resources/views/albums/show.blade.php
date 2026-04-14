@extends('layouts.app')

@section('title', trim((string) ($album->title ?? '')) !== '' ? $album->title : 'Album')

@section('content')
@php
$title = trim((string) ($album->title ?? ''));
if ($title === '') {
    $title = 'Untitled album';
}

$descriptionHtml = $album->description_html ?? null;
$ownerName = trim(sprintf('%s %s', trim((string) ($album->user?->first_name ?? '')), trim((string) ($album->user?->last_name ?? ''))));
if ($ownerName === '') {
    $ownerName = 'Unknown owner';
}

$coverPath = trim((string) ($album->coverPhoto?->path ?? ''));
$coverUrl = $coverPath === ''
    ? null
    : (str_starts_with($coverPath, 'http://') || str_starts_with($coverPath, 'https://') || str_starts_with($coverPath, '/')
        ? $coverPath
        : \Illuminate\Support\Facades\Storage::url($coverPath));

$albumPhotos = collect($album->photos ?? [])
    ->sortByDesc('id')
    ->values();
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h1 class="text-2xl font-bold text-foreground">{{ $title }}</h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.badge variant="muted" size="sm">{{ $album->is_private ? 'Private' : 'Public' }}</x-ui.badge>
                        @if($album->is_favorite)
                            <x-ui.badge variant="outline" size="sm">Favorite</x-ui.badge>
                        @endif
                        <x-ui.badge variant="outline" size="sm">{{ $albumPhotos->count() }} photo{{ $albumPhotos->count() === 1 ? '' : 's' }}</x-ui.badge>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground">By {{ $ownerName }}</p>
            </div>
        </x-slot:header>

        @if($coverUrl)
            <div class="overflow-hidden rounded border border-border bg-secondary mx-auto max-w-2xl">
                <img src="{{ $coverUrl }}" alt="{{ $title }} cover photo" class="max-h-[30rem] w-full object-cover" loading="lazy">
            </div>
        @endif

        @if(filled($descriptionHtml))
            <x-ui.markdown-content :html="$descriptionHtml" class="max-w-2xl mx-auto text-muted-foreground" />
        @else
            <p class="text-sm text-muted-foreground">No album description provided.</p>
        @endif

        <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
            @can('update', $album)
                <a
                    href="{{ route('albums.edit', $album) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Edit Album
                </a>
            @endcan

            @can('delete', $album)
                <form action="{{ route('albums.destroy', $album) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                        onclick="return confirm('Delete this album? Photos will remain in your library unless separately removed.')"
                    >
                        Delete Album
                    </button>
                </form>
            @endcan

            <a
                href="{{ auth()->check() ? route('albums.index') : route('home') }}"
                class="ml-auto inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
            >
                Back
            </a>
        </div>
    </x-ui.card>

    <section class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-bold text-foreground">Album Photos</h2>
            <div class="flex items-center gap-2">
                @include('photos.partials.slideshow-modal', [
                    'photos' => $albumPhotos,
                    'triggerLabel' => 'View slideshow',
                    'rootId' => 'album-spotlight-modal',
                    'showTrigger' => $albumPhotos->isNotEmpty(),
                ])
                <x-ui.badge variant="outline" size="sm">{{ $albumPhotos->count() }} listed</x-ui.badge>
            </div>
        </div>

        @if($albumPhotos->isEmpty())
            <x-ui.empty-state
                title="This album has no photos yet."
                description="Add photos from the edit page to populate this album."
                align="left"
                compact
            >
                <x-slot:actions>
                    @can('update', $album)
                        <a
                            href="{{ route('albums.edit', $album) }}"
                            class="inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                        >
                            Add photos
                        </a>
                    @endcan
                </x-slot:actions>
            </x-ui.empty-state>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($albumPhotos as $photo)
                    @php
                    $path = trim((string) ($photo->path ?? ''));
                    $imageUrl = $path === ''
                        ? null
                        : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
                            ? $path
                            : \Illuminate\Support\Facades\Storage::url($path));
                    $photoTitle = trim((string) ($photo->title ?? ''));
                    if ($photoTitle === '') {
                        $photoTitle = 'Photo #'.$photo->id;
                    }
                    $ratingCount = (int) collect($photo->ratings ?? [])->count();
                    $averageRating = $ratingCount > 0 ? number_format((float) collect($photo->ratings)->avg('rating'), 1) : '0.0';
                    $commentCount = (int) collect($photo->comments ?? [])->count();
                    @endphp

                    <x-ui.card as="article" padding="sm" class="space-y-3">
                        <a href="{{ route('photos.show', $photo) }}" class="block overflow-hidden rounded border border-border bg-secondary">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $photoTitle }}" class="h-44 w-full object-cover" loading="lazy">
                            @else
                                <div class="flex h-44 items-center justify-center text-sm text-muted-foreground">No preview available</div>
                            @endif
                        </a>

                        <div class="space-y-1">
                            <h3 class="text-base font-bold text-foreground">
                                <a href="{{ route('photos.show', $photo) }}" class="underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                    {{ $photoTitle }}
                                </a>
                            </h3>
                            <p class="text-sm text-muted-foreground">Average rating: {{ $averageRating }}/5</p>
                            <p class="text-xs text-muted-foreground">{{ $ratingCount }} rating{{ $ratingCount === 1 ? '' : 's' }} • {{ $commentCount }} comment{{ $commentCount === 1 ? '' : 's' }}</p>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
