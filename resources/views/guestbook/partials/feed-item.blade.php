@php
    $author = $entry->post?->user;
    $hasPhoto = $entry->photo !== null;
    $detailUrl = $hasPhoto ? route('photos.show', $entry->photo) : route('posts.show', $entry->post);
    $detailLabel = $hasPhoto ? 'Open photo' : 'Open post';
    $ratingsCount = $entry->photo?->ratings_count ?? 0;
    $commentsCount = $entry->photo?->comments_count ?? 0;
@endphp

<article class="bg-card text-card-foreground border border-border rounded p-4 sm:p-5 mb-4">
    <div class="flex items-start gap-3">
        @include('guestbook.partials.user-avatar', ['user' => $author])

        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <p class="text-sm font-bold text-foreground">
                    {{ trim(($author?->first_name ?? '').' '.($author?->last_name ?? '')) !== '' ? trim(($author?->first_name ?? '').' '.($author?->last_name ?? '')) : 'Guest' }}
                </p>
                <span class="text-xs text-muted-foreground">•</span>
                <p class="text-xs text-muted-foreground">{{ $entry->created_at?->diffForHumans() }}</p>
                <span class="inline-flex items-center gap-1 bg-secondary text-secondary-foreground text-[11px] font-bold px-2 py-1 rounded border border-border">
                    <x-icon name="{{ $hasPhoto ? 'image' : 'pen' }}" class="w-3 h-3" />
                    {{ $hasPhoto ? 'Photo entry' : 'Text entry' }}
                </span>
            </div>

            <h2 class="text-base font-bold text-foreground">{{ $entry->post->title }}</h2>
            <p class="text-sm text-foreground mt-2">{{ $entry->post->description }}</p>

            @if($hasPhoto)
                <a href="{{ route('photos.show', $entry->photo) }}" class="mt-3 block border border-border rounded overflow-hidden max-w-sm">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($entry->photo->path) }}" alt="{{ $entry->photo->title }}" class="w-full h-48 object-cover">
                </a>
            @endif

            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                <span class="inline-flex items-center gap-1">
                    <x-icon name="thumbs-up" class="w-3 h-3" />
                    {{ $entry->post->votes_count }} votes
                </span>
                <span class="inline-flex items-center gap-1">
                    <x-icon name="star" class="w-3 h-3" />
                    {{ $ratingsCount }} ratings
                </span>
                <span class="inline-flex items-center gap-1">
                    <x-icon name="menu" class="w-3 h-3" />
                    {{ $commentsCount }} comments
                </span>
                <a href="{{ $detailUrl }}" class="text-primary font-bold hover:underline">{{ $detailLabel }}</a>
            </div>
        </div>
    </div>
</article>
