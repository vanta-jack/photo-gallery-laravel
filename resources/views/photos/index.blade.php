@extends('layouts.app')

@section('title', 'Photos')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-foreground">Photos</h1>
    @auth
        <a href="{{ route('photos.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Upload Photo</a>
    @endauth
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($photos as $photo)
        <div class="bg-card text-card-foreground border border-border rounded p-4">
            <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" class="w-full h-48 object-cover rounded mb-3">
            <h3 class="font-bold text-foreground mb-1">{{ $photo->title }}</h3>
            <p class="text-muted-foreground text-sm mb-3">by {{ $photo->user->first_name ?? 'Unknown' }}</p>
            <a href="{{ route('photos.show', $photo) }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">View</a>
        </div>
    @empty
        <div class="bg-card text-card-foreground border border-border rounded p-4">
            <p class="text-foreground">No photos yet. Be the first to upload!</p>
        </div>
    @endforelse
</div>

{{-- Pagination links --}}
<div class="mt-8">
    {{ $photos->links() }}
</div>
@endsection
