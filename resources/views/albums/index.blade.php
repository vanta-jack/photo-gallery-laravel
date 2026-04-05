@extends('layouts.app')

@section('title', 'Albums')

@section('content')
<div class="flex-between mb-2">
    <h1>Albums</h1>
    @auth
        <a href="{{ route('albums.create') }}" class="btn">Create Album</a>
    @endauth
</div>

<div class="grid grid-3">
    @forelse($albums as $album)
        <div class="card">
            @if($album->coverPhoto)
                <img src="{{ asset('storage/' . $album->coverPhoto->path) }}" alt="{{ $album->title }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">
            @else
                <div style="width: 100%; height: 200px; background: #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                    📁 No cover
                </div>
            @endif
            <h3 style="margin-top: 1rem;">{{ $album->title }}</h3>
            <p class="text-muted">by {{ $album->user->first_name ?? 'Unknown' }}</p>
            @if($album->is_private)
                <span class="text-muted">🔒 Private</span>
            @endif
            <a href="{{ route('albums.show', $album) }}" class="btn" style="margin-top: 0.5rem;">View</a>
        </div>
    @empty
        <div class="card">
            <p>No albums yet. Create one to organize your photos!</p>
        </div>
    @endforelse
</div>

<div style="margin-top: 2rem;">
    {{ $albums->links() }}
</div>
@endsection
