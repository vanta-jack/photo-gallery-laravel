@extends('layouts.app')

@section('title', trim((string) ($photo->title ?? 'Photo')) !== '' ? $photo->title : 'Photo')

@section('content')
@php
$rawPath = trim((string) ($photo->path ?? ''));
$photoUrl = $rawPath === ''
    ? null
    : (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://') || str_starts_with($rawPath, '/')
        ? $rawPath
        : \Illuminate\Support\Facades\Storage::url($rawPath));

$photoTitle = trim((string) ($photo->title ?? ''));
if ($photoTitle === '') {
    $photoTitle = 'Untitled photo';
}

$photoDescriptionHtml = $photo->description_html ?? null;
$uploaderName = trim(sprintf('%s %s', trim((string) ($photo->user?->first_name ?? '')), trim((string) ($photo->user?->last_name ?? ''))));
if ($uploaderName === '') {
    $uploaderName = 'Guest uploader';
}

$uploadedLabel = $photo->created_at instanceof \Carbon\CarbonInterface
    ? $photo->created_at->format('M j, Y g:i A')
    : 'Date unavailable';

$ratings = $photo->ratings ?? collect();
$ratingCount = (int) $ratings->count();
$averageRating = $ratingCount > 0 ? (float) $ratings->avg('rating') : 0.0;

$comments = collect($photo->comments ?? [])
    ->sortByDesc('created_at')
    ->values();
$commentCount = (int) $comments->count();
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold text-foreground">{{ $photoTitle }}</h1>
                    <p class="text-sm text-muted-foreground">Uploaded by {{ $uploaderName }} • {{ $uploadedLabel }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @if($ratingCount > 0)
                        <div class="flex items-center gap-1.5 rounded border border-border bg-secondary/50 px-3 py-1.5">
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <x-icon name="star" class="w-4 h-4 text-yellow-500 fill-yellow-500" />
                                    @elseif($i - $averageRating < 1)
                                        <x-icon name="star" class="w-4 h-4 text-yellow-500" style="clip-path: polygon(0 0, {{ ($averageRating - floor($averageRating)) * 100 }}% 0, {{ ($averageRating - floor($averageRating)) * 100 }}% 100%, 0 100%);" />
                                    @else
                                        <x-icon name="star" class="w-4 h-4 text-muted-foreground" />
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs font-bold text-foreground">{{ number_format($averageRating, 1) }}</span>
                            <span class="text-xs text-muted-foreground">({{ $ratingCount }})</span>
                        </div>
                    @else
                        <x-ui.badge variant="outline" size="sm">No ratings yet</x-ui.badge>
                    @endif
                    <x-ui.badge variant="muted" size="sm">{{ $commentCount }} comment{{ $commentCount === 1 ? '' : 's' }}</x-ui.badge>
                </div>
            </div>
        </x-slot:header>

        <div class="overflow-hidden rounded border border-border bg-secondary mx-auto max-w-2xl">
            @if($photoUrl)
                <img src="{{ $photoUrl }}" alt="{{ $photoTitle }}" class="max-h-[34rem] w-full object-contain" loading="lazy">
            @else
                <div class="flex h-72 items-center justify-center text-sm text-muted-foreground">No preview available</div>
            @endif
        </div>

        @include('photos.partials.slideshow-modal', [
            'photos' => $spotlightPhotos,
            'triggerLabel' => 'View slideshow',
            'rootId' => 'photo-spotlight-modal',
            'showTrigger' => true,
        ])

        @if(filled($photoDescriptionHtml))
            <x-ui.markdown-content :html="$photoDescriptionHtml" class="max-w-2xl mx-auto text-muted-foreground" />
        @else
            <p class="text-sm text-muted-foreground">No description provided for this photo.</p>
        @endif

        <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
            @auth
                <a
                    href="{{ route('photos.comments.create', $photo) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Add Comment
                </a>

                <a
                    href="{{ route('photos.ratings.create', $photo) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Rate Photo
                </a>
            @endauth

            @can('update', $photo)
                <a
                    href="{{ route('photos.edit', $photo) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Edit
                </a>
            @endcan

            @can('delete', $photo)
                <form action="{{ route('photos.destroy', $photo) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                        onclick="return confirm('Delete this photo? This action cannot be undone.')"
                    >
                        Delete
                    </button>
                </form>
            @endcan

            <a
                href="{{ auth()->check() ? route('photos.index') : route('photos.analytics') }}"
                class="ml-auto inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
            >
                Back
            </a>
        </div>
    </x-ui.card>

    <x-ui.card class="space-y-3">
        <x-slot:header>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-foreground">Comments</h2>
                <x-ui.badge variant="outline" size="sm">{{ $commentCount }} total</x-ui.badge>
            </div>
        </x-slot:header>

        @if($comments->isEmpty())
            <x-ui.empty-state
                title="No comments yet."
                description="Be the first to share feedback on this photo."
                compact
                align="left"
            />
        @else
            <div class="space-y-3">
                @foreach($comments as $comment)
                    @php
                    $commentAuthor = trim(sprintf('%s %s', trim((string) ($comment->user?->first_name ?? '')), trim((string) ($comment->user?->last_name ?? ''))));
                    if ($commentAuthor === '') {
                        $commentAuthor = 'Anonymous';
                    }
                    $commentDate = $comment->created_at instanceof \Carbon\CarbonInterface
                        ? $comment->created_at->format('M j, Y g:i A')
                        : 'Date unavailable';
                    @endphp

                    <x-ui.card padding="sm" class="space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-bold text-foreground">{{ $commentAuthor }}</p>
                            <p class="text-xs text-muted-foreground">{{ $commentDate }}</p>
                        </div>

                        <p class="text-sm text-muted-foreground">{{ $comment->body }}</p>

                        @canany(['update', 'delete'], $comment)
                            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-3">
                                @can('update', $comment)
                                    <a
                                        href="{{ route('photos.comments.edit', [$photo, $comment]) }}"
                                        class="inline-flex items-center rounded border border-border bg-secondary px-3 py-1.5 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('delete', $comment)
                                    <form action="{{ route('photos.comments.destroy', [$photo, $comment]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded border border-destructive px-3 py-1.5 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                                            onclick="return confirm('Delete this comment?')"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @endcanany
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </x-ui.card>
</div>
@endsection
