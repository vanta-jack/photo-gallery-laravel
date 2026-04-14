@extends('layouts.app')

@section('title', 'Photo Analytics')

@section('content')
@php
$topRatedPhotos = $topRatedPhotos ?? collect();
$mostCommentedPhotos = $mostCommentedPhotos ?? collect();
$topRatedChart = is_array($topRatedChart ?? null) ? $topRatedChart : [];
$mostCommentedChart = is_array($mostCommentedChart ?? null) ? $mostCommentedChart : [];
$isMineScope = $scope === 'mine';
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-4">
        <x-slot:header>
            <div class="space-y-2">
                <h1 class="text-2xl font-bold text-foreground">Photo Analytics</h1>
                <p class="text-sm text-muted-foreground">Track top-rated and most-discussed photos across the gallery.</p>
            </div>
        </x-slot:header>

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-muted-foreground">Scope:</span>

            <a
                href="{{ route('photos.analytics', ['scope' => 'global']) }}"
                class="{{ $scope === 'global' ? 'border-primary bg-primary text-primary-foreground' : 'border-border bg-secondary text-secondary-foreground' }} inline-flex items-center rounded border px-3 py-1.5 text-xs font-bold transition-opacity duration-150 hover:opacity-90"
            >
                Global
            </a>

            @auth
                <a
                    href="{{ route('photos.analytics', ['scope' => 'mine']) }}"
                    class="{{ $isMineScope ? 'border-primary bg-primary text-primary-foreground' : 'border-border bg-secondary text-secondary-foreground' }} inline-flex items-center rounded border px-3 py-1.5 text-xs font-bold transition-opacity duration-150 hover:opacity-90"
                >
                    My photos
                </a>
            @else
                <x-ui.badge variant="muted" size="sm">Sign in to filter your photos</x-ui.badge>
            @endauth
        </div>
    </x-ui.card>

    <section class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-bold text-foreground">Top Rated Photos</h2>
            <x-ui.badge variant="outline" size="sm">{{ $topRatedPhotos->count() }} results</x-ui.badge>
        </div>

        @if($topRatedPhotos->isEmpty())
            <x-ui.empty-state
                title="No rated photos available."
                description="Ratings will appear here as soon as users score uploaded photos."
                align="left"
                compact
            />
        @else
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @foreach($topRatedPhotos as $index => $photo)
                    @php
                    $path = trim((string) ($photo->path ?? ''));
                    $photoUrl = $path === ''
                        ? null
                        : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
                            ? $path
                            : \Illuminate\Support\Facades\Storage::url($path));

                    $title = trim((string) ($photo->title ?? ''));
                    if ($title === '') {
                        $title = 'Untitled photo';
                    }

                    $averageRating = (float) ($photo->average_rating ?? 0);
                    $ratingsCount = (int) ($photo->ratings_count ?? 0);
                    $barHeight = (int) round((float) (($topRatedChart[$index]['height'] ?? 0)));
                    $ownerName = trim(sprintf('%s %s', trim((string) ($photo->user?->first_name ?? '')), trim((string) ($photo->user?->last_name ?? ''))));
                    if ($ownerName === '') {
                        $ownerName = 'Unknown uploader';
                    }
                    @endphp

                    <x-ui.card as="article" padding="sm" class="space-y-3">
                        <div class="flex items-start gap-3">
                            <a href="{{ route('photos.show', $photo) }}" class="block w-28 shrink-0 overflow-hidden rounded border border-border bg-secondary">
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="{{ $title }}" class="h-20 w-full object-cover" loading="lazy">
                                @else
                                    <div class="flex h-20 items-center justify-center text-xs text-muted-foreground">No image</div>
                                @endif
                            </a>

                            <div class="min-w-0 flex-1 space-y-1">
                                <h3 class="truncate text-base font-bold text-foreground">
                                    <a href="{{ route('photos.show', $photo) }}" class="underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                        {{ $title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-muted-foreground">By {{ $ownerName }}</p>
                                <div class="flex items-center gap-1">
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($averageRating))
                                                <x-icon name="star" class="w-3 h-3 text-yellow-500 fill-yellow-500" />
                                            @elseif($i - $averageRating < 1)
                                                <x-icon name="star" class="w-3 h-3 text-yellow-500" style="clip-path: polygon(0 0, {{ ($averageRating - floor($averageRating)) * 100 }}% 0, {{ ($averageRating - floor($averageRating)) * 100 }}% 100%, 0 100%);" />
                                            @else
                                                <x-icon name="star" class="w-3 h-3 text-muted-foreground" />
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-xs font-bold text-foreground">{{ number_format($averageRating, 1) }}</span>
                                </div>
                                <p class="text-xs text-muted-foreground">{{ $ratingsCount }} rating{{ $ratingsCount === 1 ? '' : 's' }}</p>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-muted-foreground">Rating intensity</p>
                            <div class="h-2 overflow-hidden rounded border border-border bg-secondary">
                                <div class="h-full bg-primary transition-all duration-200" style="width: {{ $barHeight }}%;"></div>
                            </div>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </section>

    <section class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-bold text-foreground">Most Commented Photos</h2>
            <x-ui.badge variant="outline" size="sm">{{ $mostCommentedPhotos->count() }} results</x-ui.badge>
        </div>

        @if($mostCommentedPhotos->isEmpty())
            <x-ui.empty-state
                title="No commented photos available."
                description="Comment activity will populate this section automatically."
                align="left"
                compact
            />
        @else
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @foreach($mostCommentedPhotos as $index => $photo)
                    @php
                    $path = trim((string) ($photo->path ?? ''));
                    $photoUrl = $path === ''
                        ? null
                        : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
                            ? $path
                            : \Illuminate\Support\Facades\Storage::url($path));

                    $title = trim((string) ($photo->title ?? ''));
                    if ($title === '') {
                        $title = 'Untitled photo';
                    }

                    $commentCount = (int) ($photo->comments_count ?? 0);
                    $ratingsCount = (int) ($photo->ratings_count ?? 0);
                    $averageRating = (float) ($photo->average_rating ?? 0);
                    $barHeight = (int) round((float) (($mostCommentedChart[$index]['height'] ?? 0)));
                    @endphp

                    <x-ui.card as="article" padding="sm" class="space-y-3">
                        <div class="flex items-start gap-3">
                            <a href="{{ route('photos.show', $photo) }}" class="block w-28 shrink-0 overflow-hidden rounded border border-border bg-secondary">
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="{{ $title }}" class="h-20 w-full object-cover" loading="lazy">
                                @else
                                    <div class="flex h-20 items-center justify-center text-xs text-muted-foreground">No image</div>
                                @endif
                            </a>

                            <div class="min-w-0 flex-1 space-y-1">
                                <h3 class="truncate text-base font-bold text-foreground">
                                    <a href="{{ route('photos.show', $photo) }}" class="underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                        {{ $title }}
                                    </a>
                                </h3>
                                <p class="text-sm font-bold text-foreground">{{ $commentCount }} comment{{ $commentCount === 1 ? '' : 's' }}</p>
                                <div class="flex items-center gap-1">
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($averageRating))
                                                <x-icon name="star" class="w-3 h-3 text-yellow-500 fill-yellow-500" />
                                            @elseif($i - $averageRating < 1)
                                                <x-icon name="star" class="w-3 h-3 text-yellow-500" style="clip-path: polygon(0 0, {{ ($averageRating - floor($averageRating)) * 100 }}% 0, {{ ($averageRating - floor($averageRating)) * 100 }}% 100%, 0 100%);" />
                                            @else
                                                <x-icon name="star" class="w-3 h-3 text-muted-foreground" />
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-xs text-muted-foreground">{{ number_format($averageRating, 1) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-muted-foreground">Comment intensity</p>
                            <div class="h-2 overflow-hidden rounded border border-border bg-secondary">
                                <div class="h-full bg-primary transition-all duration-200" style="width: {{ $barHeight }}%;"></div>
                            </div>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
