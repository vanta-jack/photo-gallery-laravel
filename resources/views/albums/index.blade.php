@extends('layouts.app')

@section('title', 'Albums')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-foreground">Albums</h1>
    @auth
        <a href="{{ route('albums.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Create Album</a>
    @endauth
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    @forelse($albums as $album)
        <div class="bg-card text-card-foreground border border-border rounded p-4">
            @if($album->coverPhoto)
                <img src="{{ asset('storage/' . $album->coverPhoto->path) }}" alt="{{ $album->title }}" class="w-full h-48 object-cover rounded mb-3">
            @else
                <div class="w-full h-48 bg-secondary rounded mb-3 flex items-center justify-center text-muted-foreground">
                    📁 No cover
                </div>
            @endif
            <h3 class="font-bold text-foreground mb-1">{{ $album->title }}</h3>
            <p class="text-muted-foreground text-sm mb-2">by {{ $album->user->first_name ?? 'Unknown' }}</p>
            @if($album->is_private)
                <span class="text-muted-foreground text-sm">🔒 Private</span>
            @endif
            <a href="{{ route('albums.show', $album) }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block mt-3">View</a>
        </div>
    @empty
        <div class="bg-card text-card-foreground border border-border rounded p-4">
            <p class="text-foreground">No albums yet. Create one to organize your photos!</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $albums->links() }}
</div>
@endsection
