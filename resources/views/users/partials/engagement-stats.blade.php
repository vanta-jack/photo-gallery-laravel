@php
$engagement = is_array($engagement ?? null) ? $engagement : [];
$summary = is_array($engagement['summary'] ?? null) ? $engagement['summary'] : [];
$topContent = is_array($engagement['top_content'] ?? null) ? $engagement['top_content'] : [];
$trend = collect($engagement['trend'] ?? [])->filter(fn ($point): bool => is_array($point))->values();

$postVotes = (int) ($summary['post_votes'] ?? 0);
$photoComments = (int) ($summary['photo_comments'] ?? 0);
$photoRatings = (int) ($summary['photo_ratings'] ?? 0);
$totalInteractions = $postVotes + $photoComments + $photoRatings;

$topPost = $topContent['post'] ?? null;
$topPhoto = $topContent['photo'] ?? null;

$topPostTitle = trim((string) ($topPost?->title ?? 'No voted posts yet.'));
$topPostVotes = (int) ($topPost?->votes_count ?? 0);

$topPhotoTitle = trim((string) ($topPhoto?->title ?? 'No rated photos yet.'));
$topPhotoRatings = (int) ($topPhoto?->ratings_count ?? 0);
$topPhotoAverage = is_numeric($topPhoto?->ratings_avg_rating)
    ? number_format((float) $topPhoto->ratings_avg_rating, 1)
    : '0.0';

$topPostUrl = $topPost?->id !== null ? route('posts.show', $topPost->id) : null;
$topPhotoUrl = $topPhoto?->id !== null ? route('photos.show', $topPhoto->id) : null;
@endphp

<x-ui.card class="space-y-6" aria-labelledby="engagement-metrics-heading">
    <x-slot:header>
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 id="engagement-metrics-heading" class="text-lg font-bold text-foreground">Engagement Metrics</h2>
            <x-ui.badge variant="outline" size="sm">{{ $totalInteractions }} total interactions</x-ui.badge>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <section class="rounded border border-border bg-secondary p-3" aria-label="Post votes summary">
            <p class="text-xs font-bold text-muted-foreground">Post Votes</p>
            <p class="mt-1 text-lg font-bold text-foreground">{{ $postVotes }}</p>
        </section>

        <section class="rounded border border-border bg-secondary p-3" aria-label="Photo comments summary">
            <p class="text-xs font-bold text-muted-foreground">Photo Comments</p>
            <p class="mt-1 text-lg font-bold text-foreground">{{ $photoComments }}</p>
        </section>

        <section class="rounded border border-border bg-secondary p-3" aria-label="Photo ratings summary">
            <p class="text-xs font-bold text-muted-foreground">Photo Ratings</p>
            <p class="mt-1 text-lg font-bold text-foreground">{{ $photoRatings }}</p>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
        <x-ui.card padding="sm" class="h-full space-y-2">
            <p class="text-xs font-bold text-muted-foreground">Top voted post</p>
            @if($topPostUrl)
                <a href="{{ $topPostUrl }}" class="text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                    {{ $topPostTitle }}
                </a>
            @else
                <p class="text-sm font-bold text-foreground">{{ $topPostTitle }}</p>
            @endif
            <p class="text-sm text-muted-foreground">{{ $topPostVotes }} total votes</p>
        </x-ui.card>

        <x-ui.card padding="sm" class="h-full space-y-2">
            <p class="text-xs font-bold text-muted-foreground">Top rated photo</p>
            @if($topPhotoUrl)
                <a href="{{ $topPhotoUrl }}" class="text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                    {{ $topPhotoTitle }}
                </a>
            @else
                <p class="text-sm font-bold text-foreground">{{ $topPhotoTitle }}</p>
            @endif
            <p class="text-sm text-muted-foreground">{{ $topPhotoRatings }} ratings • {{ $topPhotoAverage }}/5 average</p>
        </x-ui.card>
    </div>

    <section class="space-y-3" aria-label="Engagement trend">
        <h3 class="text-sm font-bold text-foreground">Engagement trend</h3>

        @if($trend->isEmpty())
            <x-ui.empty-state
                title="No engagement trend available."
                description="Engagement data will appear when interactions are recorded."
                compact
                align="left"
            />
        @else
            <ul class="space-y-2" aria-label="Monthly engagement trend list">
                @foreach($trend as $point)
                    @php
                    $label = trim((string) ($point['label'] ?? 'Unknown period'));
                    $voteCount = (int) ($point['post_votes'] ?? 0);
                    $commentCount = (int) ($point['photo_comments'] ?? 0);
                    $ratingCount = (int) ($point['photo_ratings'] ?? 0);
                    $total = (int) ($point['total_engagement'] ?? ($voteCount + $commentCount + $ratingCount));
                    $intensity = max(0, min(100, (int) ($point['intensity'] ?? 0)));
                    @endphp

                    <li class="rounded border border-border bg-background p-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-bold text-foreground">{{ $label }}</p>
                            <x-ui.badge variant="muted" size="sm">{{ $total }} interactions</x-ui.badge>
                        </div>

                        <p class="mt-1 text-xs text-muted-foreground">{{ $voteCount }} votes, {{ $commentCount }} comments, {{ $ratingCount }} ratings</p>

                        <div
                            class="mt-2 h-2 overflow-hidden rounded border border-border bg-secondary"
                            role="progressbar"
                            aria-label="Engagement intensity for {{ $label }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            aria-valuenow="{{ $intensity }}"
                        >
                            <div class="h-full bg-primary" style="width: {{ $intensity }}%;"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</x-ui.card>
