@extends('layouts.app')

@section('title', 'Guestbook')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-foreground">Guestbook</h1>
    @auth
        <a href="{{ route('guestbook.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Sign Guestbook</a>
    @endauth
</div>

@forelse($entries as $entry)
    <div class="bg-card text-card-foreground border border-border rounded p-6 mb-4">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h3 class="font-bold text-foreground text-lg mb-1">{{ $entry->post->title }}</h3>
                <p class="text-muted-foreground text-sm mb-4">{{ $entry->post->user->first_name ?? 'Guest' }} • {{ $entry->post->created_at->diffForHumans() }}</p>
            </div>
            @if($entry->photo)
                <img src="{{ asset('storage/' . $entry->photo->path) }}" alt="Photo" class="w-24 h-24 object-cover rounded ml-4">
            @endif
        </div>
        <p class="text-foreground">{{ $entry->post->description }}</p>
    </div>
@empty
    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <p class="text-foreground">No guestbook entries yet. Be the first to sign!</p>
    </div>
@endforelse

<div class="mt-8">
    {{ $entries->links() }}
</div>
@endsection
