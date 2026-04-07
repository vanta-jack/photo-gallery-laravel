@extends('layouts.app')

@section('title', 'Home')

@section('content')
<x-splash-modal />

<div class="bg-card text-card-foreground border border-border rounded p-6 mb-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">Welcome to VANITI FAIRE</h1>
            <p class="text-muted-foreground">Explore public posts, albums, milestones, and guestbook activity in one feed.</p>
        </div>
        @auth
            <details class="relative">
                <summary class="list-none cursor-pointer bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary inline-flex items-center gap-2">
                    <x-icon name="plus" class="w-4 h-4" />
                    +Create
                    <x-icon name="chevron-down" class="w-4 h-4" />
                </summary>
                <div class="absolute right-0 mt-2 w-56 rounded border border-border bg-card p-2 shadow-sm z-10">
                    <a href="{{ route('photos.create') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-bold text-foreground hover:bg-secondary rounded">
                        <x-icon name="camera" class="w-4 h-4" />
                        Upload Photo
                    </a>
                    <a href="{{ route('albums.create') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-bold text-foreground hover:bg-secondary rounded">
                        <x-icon name="folder" class="w-4 h-4" />
                        Create Album
                    </a>
                    <a href="{{ route('posts.create') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-bold text-foreground hover:bg-secondary rounded">
                        <x-icon name="pen" class="w-4 h-4" />
                        Create Post
                    </a>
                    <a href="{{ route('milestones.create') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-bold text-foreground hover:bg-secondary rounded">
                        <x-icon name="target" class="w-4 h-4" />
                        Add Milestone
                    </a>
                    <a href="{{ route('guestbook.create') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-bold text-foreground hover:bg-secondary rounded">
                        <x-icon name="pen-tool" class="w-4 h-4" />
                        Sign Guestbook
                    </a>
                </div>
            </details>
        @endauth
    </div>
</div>

<section class="bg-card text-card-foreground border border-border rounded p-4 mb-6">
    <form method="GET" action="{{ route('home') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div>
            <label for="type" class="block text-xs font-bold text-muted-foreground uppercase tracking-wide mb-2">Type</label>
            <select id="type" name="type" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                <option value="all" @selected($filters['type'] === 'all')>All Types</option>
                <option value="post" @selected($filters['type'] === 'post')>Posts</option>
                <option value="album" @selected($filters['type'] === 'album')>Albums</option>
                <option value="milestone" @selected($filters['type'] === 'milestone')>Milestones</option>
                <option value="guestbook" @selected($filters['type'] === 'guestbook')>Guestbook</option>
            </select>
        </div>
        <div>
            <label for="sort" class="block text-xs font-bold text-muted-foreground uppercase tracking-wide mb-2">Sort</label>
            <select id="sort" name="sort" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                <option value="date_desc" @selected($filters['sort'] === 'date_desc')>Newest First</option>
                <option value="date_asc" @selected($filters['sort'] === 'date_asc')>Oldest First</option>
                <option value="engagement_desc" @selected($filters['sort'] === 'engagement_desc')>Highest Engagement</option>
            </select>
        </div>
        <div class="md:col-span-2 flex items-center gap-2">
            <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                Apply Filters
            </button>
            <a href="{{ route('home') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">
                Reset
            </a>
        </div>
    </form>
</section>

<div class="space-y-4">
    @forelse($feedItems as $item)
        <article class="bg-card text-card-foreground border border-border rounded p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-xs uppercase tracking-wide text-muted-foreground mb-2">
                        <x-icon name="{{ $item['icon'] }}" class="w-4 h-4" />
                        <span>{{ ucfirst($item['type']) }}</span>
                    </div>
                    <h2 class="text-lg font-bold text-foreground">{{ $item['title'] }}</h2>
                    <p class="text-xs text-muted-foreground mt-1">By {{ $item['author'] }} • {{ $item['created_at']->diffForHumans() }}</p>
                </div>
                <span class="inline-flex items-center rounded border border-border bg-secondary px-2 py-1 text-xs font-bold text-secondary-foreground">
                    {{ $item['engagement_label'] }}
                </span>
            </div>

            @if($item['image_url'])
                <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" class="mt-3 w-full max-w-sm h-48 object-cover rounded border border-border">
            @endif

            @if($item['description_html'])
                <div class="mt-3 text-sm text-foreground leading-6 space-y-2">
                    {!! $item['description_html'] !!}
                </div>
            @endif

            @if($item['url'])
                <div class="mt-4">
                    <a href="{{ $item['url'] }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">
                        View Details
                    </a>
                </div>
            @endif
        </article>
    @empty
        <div class="bg-card text-card-foreground border border-border rounded p-8 text-center">
            <x-icon name="search" class="w-12 h-12 mx-auto mb-3 text-muted-foreground" />
            <p class="text-foreground font-bold">No public content matches your filter yet.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $feedItems->links() }}
</div>
@endsection
