@extends('layouts.app')

@section('title', 'Photos')

@section('content')
<div class="flex-between mb-2">
    <h1>Photos</h1>
    @auth
        <a href="{{ route('photos.create') }}" class="btn">Upload Photo</a>
    @endauth
</div>

<div class="grid grid-4">
    @forelse($photos as $photo)
        <div class="card">
            <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">
            <h3 style="margin-top: 1rem;">{{ $photo->title }}</h3>
            <p class="text-muted">by {{ $photo->user->first_name ?? 'Unknown' }}</p>
            <a href="{{ route('photos.show', $photo) }}" class="btn" style="margin-top: 0.5rem;">View</a>
        </div>
    @empty
        <div class="card">
            <p>No photos yet. Be the first to upload!</p>
        </div>
    @endforelse
</div>

{{-- Pagination links --}}
<div style="margin-top: 2rem;">
    {{ $photos->links() }}
</div>
@endsection
