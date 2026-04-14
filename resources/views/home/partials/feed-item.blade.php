@php
$type = trim((string) ($item['type'] ?? 'post'));
$typeLabel = match ($type) {
    'post' => 'Post',
    'album' => 'Album',
    'milestone' => 'Milestone',
    'guestbook' => 'Guestbook',
    default => ucfirst($type),
};

$title = trim((string) ($item['title'] ?? ''));
if ($title === '') {
    $title = 'Untitled entry';
}

$descriptionHtml = $item['description_html'] ?? null;
$createdAt = $item['created_at'] ?? null;
$createdLabel = $createdAt instanceof \Carbon\CarbonInterface
    ? sprintf('Published %s', $createdAt->format('M j, Y'))
    : 'Publication date unavailable';

$authorUser = $item['author_user'] ?? null;
$authorName = trim((string) ($item['author'] ?? 'Guest'));
if ($authorName === '') {
    $authorName = 'Guest';
}

$authorUrl = $authorUser?->id ? route('users.show', $authorUser->id) : null;

$engagementLabel = trim((string) ($item['engagement_label'] ?? '0 interactions'));
if ($engagementLabel === '') {
    $engagementLabel = '0 interactions';
}

$itemUrl = trim((string) ($item['url'] ?? ''));
$imageUrl = trim((string) ($item['image_url'] ?? ''));
@endphp

<x-ui.card as="article" padding="sm" class="space-y-4">
    <div class="flex items-start justify-between gap-3">
        @if($authorUrl)
            <a href="{{ $authorUrl }}" class="min-w-0" aria-label="Open {{ $authorName }} profile">
                @include('guestbook.partials.user-avatar', [
                    'user' => $authorUser,
                    'name' => $authorName,
                    'meta' => $createdLabel,
                ])
            </a>
        @else
            @include('guestbook.partials.user-avatar', [
                'user' => $authorUser,
                'name' => $authorName,
                'meta' => $createdLabel,
            ])
        @endif

        <x-ui.badge-type :type="$type" size="sm" />
    </div>

    <div class="space-y-2">
        <h2 class="text-base font-bold text-foreground">{{ $title }}</h2>

        @if($descriptionHtml)
            <x-ui.markdown-content :html="$descriptionHtml" />
        @else
            <p class="text-sm text-muted-foreground">No description provided.</p>
        @endif
    </div>

    @if($imageUrl !== '')
        <div class="overflow-hidden rounded border border-border bg-secondary">
            <img
                src="{{ $imageUrl }}"
                alt="Preview image for {{ $title }}"
                class="h-44 w-full object-cover"
                loading="lazy"
            >
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-2">
        <x-ui.badge variant="muted" size="sm">{{ $engagementLabel }}</x-ui.badge>

        @if($itemUrl !== '')
            <a
                href="{{ $itemUrl }}"
                class="inline-flex items-center text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80"
            >
                View item
            </a>
        @endif
    </div>
</x-ui.card>
