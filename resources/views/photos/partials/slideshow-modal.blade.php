@php
$photoSource = $photos ?? ($ownedPhotos ?? []);
$photoCollection = $photoSource instanceof \Illuminate\Support\Collection ? $photoSource : collect($photoSource);

$showTrigger = (bool) ($showTrigger ?? true);
$triggerLabel = trim((string) ($triggerLabel ?? 'Open slideshow'));
$rootId = trim((string) ($rootId ?? 'photo-slideshow-modal'));

$slideshowPhotos = $photoCollection
    ->map(function ($photo): ?array {
        if (! is_object($photo)) {
            return null;
        }

        $path = trim((string) ($photo->path ?? ''));
        if ($path === '') {
            return null;
        }

        $url = str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
            ? $path
            : \Illuminate\Support\Facades\Storage::url($path);

        $createdAtLabel = '';
        if ($photo->created_at instanceof \Carbon\CarbonInterface) {
            $createdAtLabel = $photo->created_at->format('M j, Y');
        } elseif (is_string($photo->created_at ?? null) && trim((string) $photo->created_at) !== '') {
            $timestamp = strtotime((string) $photo->created_at);
            if ($timestamp !== false) {
                $createdAtLabel = date('M j, Y', $timestamp);
            }
        }

        $showUrl = ($photo->id ?? null) !== null ? route('photos.show', $photo->id) : '#';

        $title = trim((string) ($photo->title ?? ''));
        if ($title === '') {
            $title = 'Photo';
        }

        $descriptionHtml = $photo->description_html ?? \App\Support\MarkdownRenderer::toSafeHtml((string) ($photo->description ?? ''));

        return [
            'id' => (int) ($photo->id ?? 0),
            'url' => $url,
            'title' => $title,
            'description_html' => (string) ($descriptionHtml ?? ''),
            'created_at' => $createdAtLabel,
            'show_url' => $showUrl,
        ];
    })
    ->filter()
    ->values();

$hasPhotos = $slideshowPhotos->isNotEmpty();
@endphp

@if($showTrigger && $hasPhotos)
    <x-ui.button
        type="button"
        size="sm"
        data-slideshow-open
        aria-controls="{{ $rootId }}"
        aria-haspopup="dialog"
    >
        <x-icon name="images" class="h-4 w-4" />
        {{ $triggerLabel !== '' ? $triggerLabel : 'Open slideshow' }}
    </x-ui.button>
@endif

<div
    id="{{ $rootId }}"
    data-slideshow-root
    class="fixed inset-0 z-50 hidden bg-background/95 px-4 py-4"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-label="Photo slideshow"
>
    <script type="application/json" data-slideshow-photos>{!! $slideshowPhotos->toJson(JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

    <div class="mx-auto flex h-full w-full max-w-5xl flex-col gap-3">
        <div class="flex flex-wrap items-center justify-between gap-2" data-slideshow-controls>
            <div class="flex items-center gap-2">
                <x-ui.badge variant="muted" size="sm" data-slideshow-counter>0 / 0</x-ui.badge>
                <span data-slideshow-date class="text-xs text-muted-foreground" aria-live="polite"></span>
            </div>

            <div class="flex items-center gap-2">
                <x-ui.button type="button" variant="secondary" size="sm" data-slideshow-toggle-autoplay aria-label="Toggle autoplay">
                    Play
                </x-ui.button>

                <x-ui.button type="button" variant="secondary" size="sm" data-slideshow-close aria-label="Close slideshow">
                    Close
                </x-ui.button>
            </div>
        </div>

        <button
            type="button"
            class="relative flex flex-1 items-center justify-center overflow-hidden rounded border border-border bg-card"
            data-slideshow-stage
            aria-label="Toggle slideshow controls"
        >
            <img
                data-slideshow-image
                src=""
                alt="Slideshow photo"
                class="max-h-full max-w-full object-contain"
            >
        </button>

        <div class="grid grid-cols-1 gap-3 rounded border border-border bg-card p-4 md:grid-cols-[auto_1fr_auto] md:items-center" data-slideshow-controls>
            <x-ui.button type="button" variant="secondary" size="sm" data-slideshow-prev aria-label="Previous photo">
                <x-icon name="chevron-left" class="h-4 w-4" />
                Previous
            </x-ui.button>

            <div class="min-w-0 space-y-1 text-left">
                <p data-slideshow-title class="truncate text-sm font-bold text-foreground">Photo</p>
                <div data-slideshow-description class="prose prose-sm max-w-none text-muted-foreground prose-headings:text-foreground prose-strong:text-foreground prose-a:text-foreground prose-ul:list-disc prose-ol:list-decimal prose-li:marker:text-foreground prose-blockquote:border-l-border prose-blockquote:text-muted-foreground">No description provided.</div>
                <a
                    data-slideshow-detail-link
                    href="#"
                    class="inline-flex text-xs font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80"
                >
                    View photo detail
                </a>
            </div>

            <x-ui.button type="button" variant="secondary" size="sm" data-slideshow-next aria-label="Next photo" class="md:justify-self-end">
                Next
                <x-icon name="chevron-right" class="h-4 w-4" />
            </x-ui.button>
        </div>
    </div>
</div>

@if($showTrigger && ! $hasPhotos)
    <x-ui.alert
        class="mt-3"
        variant="muted"
        title="No slideshow items available."
        description="Submit photos to enable slideshow mode."
    />
@endif

