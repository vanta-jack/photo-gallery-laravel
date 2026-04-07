@extends('layouts.app')

@section('title', 'Photo Analytics')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-foreground inline-flex items-center gap-2">
            <x-icon name="grid" class="w-6 h-6" />
            Photo Analytics
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Discover the highest-rated and most-discussed photos.</p>
        <div class="mt-4 inline-flex items-center gap-2 rounded border border-border bg-card p-1">
            <a
                href="{{ route('photos.analytics', ['scope' => 'global']) }}"
                class="px-3 py-1 text-xs font-bold rounded {{ $scope === 'global' ? 'bg-primary text-primary-foreground' : 'text-foreground hover:bg-muted' }}"
            >
                Global
            </a>
            @auth
                <a
                    href="{{ route('photos.analytics', ['scope' => 'mine']) }}"
                    class="px-3 py-1 text-xs font-bold rounded {{ $scope === 'mine' ? 'bg-primary text-primary-foreground' : 'text-foreground hover:bg-muted' }}"
                >
                    My Photos
                </a>
            @else
                <span class="px-3 py-1 text-xs text-muted-foreground">Login to filter by your photos</span>
            @endauth
        </div>
    </div>

    <section class="grid gap-6 md:grid-cols-2">
        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4">Top Rated Photos</h2>
            <div class="h-32 border border-border rounded p-2 mb-4 bg-background/40">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full">
                    @foreach($topRatedChart as $bar)
                        <rect
                            x="{{ $bar['x'] }}"
                            y="{{ 100 - $bar['height'] }}"
                            width="{{ $bar['width'] }}"
                            height="{{ $bar['height'] }}"
                            class="fill-current text-primary"
                        />
                    @endforeach
                </svg>
            </div>
            <div class="space-y-3">
                @forelse($topRatedPhotos as $photo)
                    <div class="border border-border rounded p-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->path) }}" alt="{{ $photo->title }}" class="w-16 h-16 rounded object-cover">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('photos.show', $photo) }}" class="font-bold text-foreground hover:opacity-80 transition-opacity block truncate">{{ $photo->title }}</a>
                                <p class="text-xs text-muted-foreground">by {{ $photo->user->first_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-muted-foreground mt-1">
                                    {{ number_format((float) ($photo->average_rating ?? 0), 1) }}/5 • {{ $photo->ratings_count }} ratings • {{ $photo->comments_count }} comments
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 h-2 rounded bg-muted">
                            <div class="h-2 rounded bg-primary" style="width: {{ (int) round(((float) ($photo->average_rating ?? 0) / 5) * 100) }}%;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">No ratings submitted yet.</p>
                @endforelse
            </div>
        </article>

        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4">Most Commented Photos</h2>
            <div class="h-32 border border-border rounded p-2 mb-4 bg-background/40">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full">
                    @foreach($mostCommentedChart as $bar)
                        <rect
                            x="{{ $bar['x'] }}"
                            y="{{ 100 - $bar['height'] }}"
                            width="{{ $bar['width'] }}"
                            height="{{ $bar['height'] }}"
                            class="fill-current text-secondary-foreground"
                        />
                    @endforeach
                </svg>
            </div>
            <div class="space-y-3">
                @forelse($mostCommentedPhotos as $photo)
                    <div class="border border-border rounded p-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->path) }}" alt="{{ $photo->title }}" class="w-16 h-16 rounded object-cover">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('photos.show', $photo) }}" class="font-bold text-foreground hover:opacity-80 transition-opacity block truncate">{{ $photo->title }}</a>
                                <p class="text-xs text-muted-foreground">by {{ $photo->user->first_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-muted-foreground mt-1">
                                    {{ $photo->comments_count }} comments • {{ $photo->ratings_count }} ratings • {{ number_format((float) ($photo->average_rating ?? 0), 1) }}/5 average
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 h-2 rounded bg-muted">
                            <div class="h-2 rounded bg-secondary" style="width: {{ (int) round(((int) $photo->comments_count / $mostCommentedScale) * 100) }}%;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">No comments posted yet.</p>
                @endforelse
            </div>
        </article>
    </section>
</div>
@endsection
