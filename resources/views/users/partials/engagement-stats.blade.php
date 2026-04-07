<section class="bg-card text-card-foreground border border-border rounded p-6 space-y-6">
    <div>
        <h2 class="text-lg font-bold text-foreground">Engagement Metrics</h2>
        <p class="text-sm text-muted-foreground mt-1">Community interactions across posts and photos.</p>
    </div>

    <div class="grid gap-3 sm:grid-cols-3">
        <article class="border border-border rounded p-4 bg-muted/20">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Post Votes</p>
            <p class="mt-2 text-2xl font-bold text-foreground">{{ $engagement['summary']['post_votes'] }}</p>
        </article>
        <article class="border border-border rounded p-4 bg-muted/20">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Photo Comments</p>
            <p class="mt-2 text-2xl font-bold text-foreground">{{ $engagement['summary']['photo_comments'] }}</p>
        </article>
        <article class="border border-border rounded p-4 bg-muted/20">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Photo Ratings</p>
            <p class="mt-2 text-2xl font-bold text-foreground">{{ $engagement['summary']['photo_ratings'] }}</p>
        </article>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <article class="border border-border rounded p-4">
            <h3 class="text-sm font-bold text-foreground mb-2">Top Post</h3>
            @if($engagement['top_content']['post'])
                <p class="font-bold text-foreground">{{ $engagement['top_content']['post']->title }}</p>
                <p class="text-sm text-muted-foreground mt-1">{{ $engagement['top_content']['post']->votes_count }} total votes</p>
            @else
                <p class="text-sm text-muted-foreground">No post engagement yet.</p>
            @endif
        </article>
        <article class="border border-border rounded p-4">
            <h3 class="text-sm font-bold text-foreground mb-2">Top Photo</h3>
            @if($engagement['top_content']['photo'])
                <p class="font-bold text-foreground">{{ $engagement['top_content']['photo']->title }}</p>
                <p class="text-sm text-muted-foreground mt-1">
                    {{ $engagement['top_content']['photo']->ratings_count }} ratings • {{ number_format($engagement['top_content']['photo']->ratings_avg_rating ?? 0, 1) }}/5 average
                </p>
                <p class="text-xs text-muted-foreground mt-1">{{ $engagement['top_content']['photo']->comments_count }} comments</p>
            @else
                <p class="text-sm text-muted-foreground">No photo engagement yet.</p>
            @endif
        </article>
    </div>

    <div>
        <h3 class="text-sm font-bold text-foreground mb-3">6-Month Engagement Trend</h3>
        <div class="space-y-2">
            @foreach($engagement['trend'] as $point)
                <div class="border border-border rounded p-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-foreground">{{ $point['label'] }}</span>
                        <span class="text-muted-foreground">{{ $point['total_engagement'] }} interactions</span>
                    </div>
                    <div class="mt-2 h-2 rounded bg-muted">
                        <div class="h-2 rounded bg-primary" style="width: {{ $point['intensity'] }}%;"></div>
                    </div>
                    <p class="text-xs text-muted-foreground mt-2">
                        {{ $point['post_votes'] }} votes, {{ $point['photo_comments'] }} comments, {{ $point['photo_ratings'] }} ratings
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
