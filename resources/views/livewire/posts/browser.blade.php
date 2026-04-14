@php
$sortOptions = [
    'latest' => 'Newest first',
    'oldest' => 'Oldest first',
    'votes_desc' => 'Most votes',
];
@endphp

<div class="space-y-6">
    <x-ui.card>
        <x-slot:header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">My Posts</h1>
                    <p class="text-sm text-muted-foreground">Manage authored posts and review engagement activity.</p>
                </div>
                <x-ui.button as="a" href="{{ route('posts.create') }}">Create Post</x-ui.button>
            </div>
        </x-slot:header>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
            <x-ui.form-input
                wire:model.live.debounce.300ms="search"
                label="Search posts"
                placeholder="Search title or description"
                help="Filters update automatically as you type."
            />

            <x-ui.form-select
                wire:model.live="sort"
                label="Sort by"
                :options="$sortOptions"
            />
        </div>
    </x-ui.card>

    <x-ui.pagination-shell
        :paginator="$posts"
        emptyTitle="No posts found."
        emptyDescription="Create a post to begin publishing updates."
    >
        <div class="space-y-3">
            @foreach($posts as $post)
                <article class="rounded border border-border bg-card p-4 text-card-foreground">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        @include('guestbook.partials.user-avatar', [
                            'user' => $post->user,
                            'meta' => $post->created_at?->format('M j, Y') ? 'Published '.$post->created_at->format('M j, Y') : 'Publication date unavailable',
                        ])

                        <x-ui.badge variant="muted" size="sm">{{ (int) $post->votes_count }} votes</x-ui.badge>
                    </div>

                    <div class="mt-3 space-y-2">
                        <h2 class="text-base font-bold text-foreground">{{ $post->title }}</h2>

                        @if(filled($post->description_html))
                            <x-ui.markdown-content :html="$post->description_html" class="text-muted-foreground" />
                        @else
                            <p class="text-sm text-muted-foreground">No description provided.</p>
                        @endif
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-border pt-3">
                        <x-ui.button as="a" size="sm" variant="secondary" href="{{ route('posts.show', $post) }}">View</x-ui.button>
                        @can('update', $post)
                            <x-ui.button as="a" size="sm" variant="secondary" href="{{ route('posts.edit', $post) }}">Edit</x-ui.button>
                        @endcan
                        @can('delete', $post)
                            <form method="POST" action="{{ route('posts.destroy', $post) }}">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" size="sm" variant="destructive" onclick="return confirm('Delete this post?')">
                                    Delete
                                </x-ui.button>
                            </form>
                        @endcan
                    </div>
                </article>
            @endforeach
        </div>

        <x-slot:links>
            {{ $posts->onEachSide(1)->links() }}
        </x-slot:links>
    </x-ui.pagination-shell>
</div>
