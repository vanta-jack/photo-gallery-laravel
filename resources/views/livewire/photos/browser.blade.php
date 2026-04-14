@php
$sortOptions = [
    'latest' => 'Newest first',
    'oldest' => 'Oldest first',
    'rating_desc' => 'Highest rating',
    'rating_asc' => 'Lowest rating',
];
@endphp

<div class="space-y-6">
    <x-ui.card>
        <x-slot:header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">My Photos</h1>
                    <p class="text-sm text-muted-foreground">Review your uploads, manage metadata, and share your best shots.</p>
                </div>
                <x-ui.button as="a" href="{{ route('photos.create') }}">Upload Photo</x-ui.button>
            </div>
        </x-slot:header>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
            <x-ui.form-input
                wire:model.live.debounce.300ms="search"
                label="Search photos"
                placeholder="Search title or description"
                help="Filters update automatically as you type."
            />

            <x-ui.form-select
                wire:model.live="sort"
                label="Sort by"
                :options="$sortOptions"
            />
        </div>

        <p class="mt-3 text-sm text-muted-foreground">
            <x-ui.badge variant="outline" size="sm">
                {{ (int) $photos->total() }} photo{{ (int) $photos->total() === 1 ? '' : 's' }}
            </x-ui.badge>
        </p>
    </x-ui.card>

    <x-ui.pagination-shell
        :paginator="$photos"
        emptyTitle="No photos found."
        emptyDescription="Create a photo to begin building your gallery."
    >
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($photos as $photo)
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
                $uploadedLabel = $photo->created_at instanceof \Carbon\CarbonInterface
                    ? $photo->created_at->format('M j, Y')
                    : 'Date unavailable';

                $avgRating = (float) ($photo->ratings_avg_rating ?? 0);
                $ratingCount = (int) ($photo->ratings_count ?? 0);
                @endphp

                <x-ui.card as="article" padding="sm" class="space-y-3">
                    <a
                        href="{{ route('photos.show', $photo) }}"
                        class="block overflow-hidden rounded border border-border bg-secondary"
                        aria-label="Open {{ $photoTitle }}"
                    >
                        @if($photoUrl)
                            <img
                                src="{{ $photoUrl }}"
                                alt="{{ $photoTitle }}"
                                class="h-44 w-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="flex h-44 items-center justify-center text-sm text-muted-foreground">
                                No preview available
                            </div>
                        @endif
                    </a>

                    <div class="space-y-1">
                        <h2 class="text-base font-bold text-foreground">
                            <a href="{{ route('photos.show', $photo) }}" class="underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                {{ $photoTitle }}
                            </a>
                        </h2>
                        @if(filled($photoDescriptionHtml))
                            <x-ui.markdown-content :html="$photoDescriptionHtml" class="text-muted-foreground" />
                        @else
                            <p class="text-sm text-muted-foreground">No description provided.</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between border-t border-border pt-2">
                        <span class="text-xs text-muted-foreground">{{ $uploadedLabel }}</span>
                        @if($ratingCount > 0)
                            <div class="flex items-center gap-1">
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($avgRating))
                                            <x-icon name="star" class="w-3.5 h-3.5 text-yellow-500 fill-yellow-500" />
                                        @elseif($i - $avgRating < 1)
                                            <x-icon name="star" class="w-3.5 h-3.5 text-yellow-500" style="clip-path: polygon(0 0, {{ ($avgRating - floor($avgRating)) * 100 }}% 0, {{ ($avgRating - floor($avgRating)) * 100 }}% 100%, 0 100%);" />
                                        @else
                                            <x-icon name="star" class="w-3.5 h-3.5 text-muted-foreground" />
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-xs font-bold text-muted-foreground">{{ number_format($avgRating, 1) }}</span>
                            </div>
                        @else
                            <span class="text-xs text-muted-foreground">No ratings</span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2 border-t border-border pt-3">
                        <a
                            href="{{ route('photos.show', $photo) }}"
                            class="inline-flex items-center rounded border border-border bg-secondary px-3 py-1.5 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                        >
                            View
                        </a>

                        <a
                            href="{{ route('photos.edit', $photo) }}"
                            class="inline-flex items-center rounded border border-border bg-secondary px-3 py-1.5 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                        >
                            Edit
                        </a>

                        <form action="{{ route('photos.destroy', $photo) }}" method="POST" class="ml-auto">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center rounded border border-destructive px-3 py-1.5 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                                onclick="return confirm('Delete this photo? This action cannot be undone.')"
                            >
                                Delete
                            </button>
                        </form>
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    </x-ui.pagination-shell>
</div>
