@php
$sortOptions = [
    'latest' => 'Newest first',
    'oldest' => 'Oldest first',
];
@endphp

<div class="space-y-4">
    <x-ui.card class="space-y-4" aria-labelledby="guestbook-feed-heading">
        <x-slot:header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="space-y-1">
                    <h1 id="guestbook-feed-heading" class="text-2xl font-bold text-foreground">Guestbook Feed</h1>
                    <p class="text-sm text-muted-foreground">
                        Read recent visitor notes and shared highlights from the community.
                    </p>
                </div>

                <x-ui.badge variant="outline" size="sm">{{ $entries->total() }} entries</x-ui.badge>
            </div>
        </x-slot:header>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,220px)_auto]" aria-label="Guestbook feed filters">
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Search title, description, or author"
                        class="block w-full rounded border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-background border-input focus-visible:ring-ring"
                        aria-label="Search guestbook"
                    />
                </div>
                <button
                    type="button"
                    title="Search guestbook"
                    class="inline-flex items-center justify-center rounded border border-input bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary"
                    aria-label="Search guestbook"
                >
                    <x-icon name="search" class="w-4 h-4" />
                </button>
            </div>

            <x-ui.form-select
                wire:model.live="sort"
                label="Sort by"
                :options="$sortOptions"
            />

            <div class="flex items-end gap-2">
                <x-ui.button type="button" wire:click="clearFilters">Reset</x-ui.button>
                <x-ui.button as="a" href="{{ route('guestbook.create') }}">Write an entry</x-ui.button>
            </div>
        </div>
    </x-ui.card>

    <x-ui.pagination-shell
        :paginator="$entries"
        emptyTitle="No guestbook entries found."
        emptyDescription="Try a different search term or add a new entry."
    >
        <ul class="columns-1 gap-4 space-y-0 md:columns-2 xl:columns-3 2xl:columns-4" aria-label="Guestbook entries">
            @foreach($entries as $entry)
                <li wire:key="guestbook-feed-item-{{ $entry->id }}" class="mb-4 break-inside-avoid space-y-2">
                    @include('guestbook.partials.feed-item', ['entry' => $entry])

                    @can('update', $entry)
                        <div class="flex justify-end">
                            <a
                                href="{{ route('guestbook.edit', $entry) }}"
                                class="text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80"
                            >
                                Edit entry
                            </a>
                        </div>
                    @endcan
                </li>
            @endforeach
        </ul>

        <x-slot:links>
            {{ $entries->onEachSide(1)->links() }}
        </x-slot:links>
    </x-ui.pagination-shell>
</div>
