@extends('layouts.app')

@section('title', 'Photo Analytics')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-foreground">Photo Analytics</h1>
        <p class="text-sm text-muted-foreground mt-1">Discover the highest-rated and most-discussed photos.</p>
    </div>

    <section class="grid gap-6 md:grid-cols-2">
        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4">Top Rated Photos</h2>
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
