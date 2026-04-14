@extends('layouts.app')

@section('title', 'My Albums')

@section('content')
@php
$totalAlbums = method_exists($albums, 'total') ? (int) $albums->total() : (int) $albums->count();
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-4">
        <x-slot:header>
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold text-foreground">My Albums</h1>
                    <p class="text-sm text-muted-foreground">Group related photos, set privacy, and highlight your favorites.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge variant="outline" size="sm">
                        {{ $totalAlbums }} album{{ $totalAlbums === 1 ? '' : 's' }}
                    </x-ui.badge>

                    <a
                        href="{{ route('albums.create') }}"
                        class="inline-flex items-center rounded border border-primary bg-primary px-4 py-2 text-sm font-bold text-primary-foreground transition-opacity duration-150 hover:opacity-90"
                    >
                        Create Album
                    </a>
                </div>
            </div>
        </x-slot:header>
    </x-ui.card>

    @if($albums->isEmpty())
        <x-ui.empty-state
            title="No albums yet."
            description="Create your first album to start organizing your photo collection."
        >
            <x-slot:actions>
                <a
                    href="{{ route('albums.create') }}"
                    class="inline-flex items-center rounded border border-primary bg-primary px-4 py-2 text-sm font-bold text-primary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Create first album
                </a>
            </x-slot:actions>
        </x-ui.empty-state>
    @else
        <x-ui.pagination-shell :paginator="$albums">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($albums as $album)
                    @php
                    $coverPath = trim((string) ($album->coverPhoto?->path ?? ''));
                    $coverUrl = $coverPath === ''
                        ? null
                        : (str_starts_with($coverPath, 'http://') || str_starts_with($coverPath, 'https://') || str_starts_with($coverPath, '/')
                            ? $coverPath
                            : \Illuminate\Support\Facades\Storage::url($coverPath));

                    $title = trim((string) ($album->title ?? ''));
                    if ($title === '') {
                        $title = 'Untitled album';
                    }

                    $descriptionHtml = $album->description_html ?? null;
                    $createdLabel = $album->created_at instanceof \Carbon\CarbonInterface
                        ? $album->created_at->format('M j, Y')
                        : 'Date unavailable';
                    @endphp

                    <x-ui.card as="article" padding="sm" class="space-y-3">
                        <a
                            href="{{ route('albums.show', $album) }}"
                            class="block overflow-hidden rounded border border-border bg-secondary"
                            aria-label="Open {{ $title }}"
                        >
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" alt="{{ $title }} cover photo" class="h-44 w-full object-cover" loading="lazy">
                            @else
                                <div class="flex h-44 items-center justify-center text-sm text-muted-foreground">No cover photo selected</div>
                            @endif
                        </a>

                        <div class="space-y-1">
                            <h2 class="text-base font-bold text-foreground">
                                <a href="{{ route('albums.show', $album) }}" class="underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                    {{ $title }}
                                </a>
                            </h2>
                            @if(filled($descriptionHtml))
                                <x-ui.markdown-content :html="$descriptionHtml" class="text-muted-foreground" />
                            @else
                                <p class="text-sm text-muted-foreground">No album description provided.</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.badge variant="muted" size="sm">{{ $album->is_private ? 'Private' : 'Public' }}</x-ui.badge>
                            @if($album->is_favorite)
                                <x-ui.badge variant="outline" size="sm">Favorite</x-ui.badge>
                            @endif
                            <span class="ml-auto text-xs text-muted-foreground">{{ $createdLabel }}</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 border-t border-border pt-3">
                            <a
                                href="{{ route('albums.show', $album) }}"
                                class="inline-flex items-center rounded border border-border bg-secondary px-3 py-1.5 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                            >
                                View
                            </a>

                            <a
                                href="{{ route('albums.edit', $album) }}"
                                class="inline-flex items-center rounded border border-border bg-secondary px-3 py-1.5 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                            >
                                Edit
                            </a>

                            <form action="{{ route('albums.destroy', $album) }}" method="POST" class="ml-auto">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded border border-destructive px-3 py-1.5 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                                    onclick="return confirm('Delete this album? Photos stay available in your gallery.')"
                                >
                                    Delete
                                </button>
                            </form>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        </x-ui.pagination-shell>
    @endif
</div>
@endsection
