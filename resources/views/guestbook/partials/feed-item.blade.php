@php
/** @var \App\Models\GuestbookEntry|null $entry */
$entry = $entry ?? null;
$post = $post ?? $entry?->post;
$photo = $photo ?? $entry?->photo;
$author = $author ?? $post?->user;

$title = trim((string) ($post?->title ?? ''));
if ($title === '') {
    $title = 'Untitled entry';
}

$descriptionHtml = $post?->description_html ?? null;

$createdAt = $entry?->created_at ?? $post?->created_at;
$createdLabel = $createdAt instanceof \Carbon\CarbonInterface
    ? sprintf('Published %s', $createdAt->format('M j, Y'))
    : 'Publication date unavailable';

$isPhotoEntry = $photo !== null;
$typeLabel = $isPhotoEntry ? 'Photo entry' : 'Text entry';

$votesCount = (int) ($post?->votes_count ?? 0);
$ratingsCount = (int) ($photo?->ratings_count ?? 0);
$commentsCount = (int) ($photo?->comments_count ?? 0);

$photoPreviewUrl = null;
$photoPath = trim((string) ($photo?->path ?? ''));
if ($photoPath !== '') {
    $photoPreviewUrl = str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://') || str_starts_with($photoPath, '/')
        ? $photoPath
        : \Illuminate\Support\Facades\Storage::url($photoPath);
}

$detailUrl = $photo?->id !== null
    ? route('photos.show', $photo->id)
    : ($post?->id !== null ? route('posts.show', $post->id) : null);

$authorUrl = $author?->id !== null ? route('users.show', $author->id) : null;
@endphp

<x-ui.card as="article" padding="sm" class="space-y-4" aria-label="Guestbook feed item">
    <div class="flex items-start justify-between gap-3">
        @if($authorUrl)
            <a href="{{ $authorUrl }}" class="min-w-0" aria-label="Open {{ trim((string) ($author?->first_name ?? '')) }} {{ trim((string) ($author?->last_name ?? '')) }} profile">
                @include('guestbook.partials.user-avatar', [
                    'user' => $author,
                    'photoPath' => $author?->profilePhoto?->path,
                    'meta' => $createdLabel,
                ])
            </a>
        @else
            @include('guestbook.partials.user-avatar', [
                'user' => $author,
                'photoPath' => $author?->profilePhoto?->path,
                'meta' => $createdLabel,
            ])
        @endif

        <x-ui.badge variant="outline" size="sm">{{ $typeLabel }}</x-ui.badge>
    </div>

    <div class="space-y-2">
        <h3 class="text-base font-bold text-foreground">{{ $title }}</h3>
        @if(filled($descriptionHtml))
            <x-ui.markdown-content :html="$descriptionHtml" class="text-muted-foreground" />
        @else
            <p class="text-sm text-muted-foreground">No description provided.</p>
        @endif
    </div>

    @if($photoPreviewUrl)
        <div class="overflow-hidden rounded border border-border bg-secondary">
            <img
                src="{{ $photoPreviewUrl }}"
                alt="Preview image for {{ $title }}"
                class="h-44 w-full object-cover"
                loading="lazy"
            >
        </div>
    @endif

    <ul class="flex flex-wrap items-center gap-2" aria-label="Engagement metrics">
        <li><x-ui.badge variant="muted" size="sm">{{ $votesCount }} votes</x-ui.badge></li>
        @if($isPhotoEntry)
            <li><x-ui.badge variant="muted" size="sm">{{ $ratingsCount }} ratings</x-ui.badge></li>
            <li><x-ui.badge variant="muted" size="sm">{{ $commentsCount }} comments</x-ui.badge></li>
        @endif
    </ul>

    @if($detailUrl)
        <a
            href="{{ $detailUrl }}"
            class="inline-flex items-center text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80"
            aria-label="Open full detail for {{ $title }}"
        >
            View details
        </a>
    @endif
</x-ui.card>
