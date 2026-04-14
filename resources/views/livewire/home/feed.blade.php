@php
$typeOptions = [
    'all' => 'All activity',
    'post' => 'Posts',
    'album' => 'Albums',
    'milestone' => 'Milestones',
    'guestbook' => 'Guestbook',
];

$sortOptions = [
    'date_desc' => 'Newest first',
    'date_asc' => 'Oldest first',
    'engagement_desc' => 'Most engagement',
];
@endphp

<div class="space-y-4">
    <x-ui.card class="space-y-4" aria-labelledby="home-feed-heading">
        <x-slot:header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="space-y-1">
                    <h1 id="home-feed-heading" class="text-2xl font-bold text-foreground">Home Feed</h1>
                    <p class="text-sm text-muted-foreground">
                        Browse recent public activity from posts, albums, milestones, and guestbook entries.
                    </p>
                </div>

                <x-ui.badge variant="outline" size="sm">{{ $feedItems->total() }} items</x-ui.badge>
            </div>
        </x-slot:header>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_auto_minmax(0,220px)_minmax(0,220px)_auto]" aria-label="Home feed filters">
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Search posts, albums, milestones, guestbook, or author"
                        class="block w-full rounded border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-background border-input focus-visible:ring-ring"
                        aria-label="Search feed"
                    />
                </div>
                <button
                    type="button"
                    title="Search feed"
                    class="inline-flex items-center justify-center rounded border border-input bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary"
                    aria-label="Search feed results"
                >
                    <x-icon name="search" class="w-4 h-4" />
                </button>
            </div>

            <x-ui.form-select
                wire:model.live="type"
                label="Content type"
                :options="$typeOptions"
            />

            <x-ui.form-select
                wire:model.live="sort"
                label="Sort by"
                :options="$sortOptions"
            />

            <div class="flex items-end gap-2">
                <x-ui.button type="button" wire:click="clearFilters">Reset</x-ui.button>
            </div>
        </div>
    </x-ui.card>

    <x-ui.pagination-shell
        :paginator="$feedItems"
        emptyTitle="No feed activity yet."
        emptyDescription="Try a different search term or filter."
    >
        <ul class="columns-1 gap-4 space-y-0 md:columns-2 xl:columns-3 2xl:columns-4" aria-label="Home feed results">
            @foreach($feedItems as $item)
                <li wire:key="home-feed-item-{{ $item['type'] }}-{{ $item['id'] }}" class="mb-4 break-inside-avoid">
                    @include('home.partials.feed-item', ['item' => $item])
                </li>
            @endforeach
        </ul>

        <x-slot:links>
            {{ $feedItems->onEachSide(1)->links() }}
        </x-slot:links>
    </x-ui.pagination-shell>
</div>
