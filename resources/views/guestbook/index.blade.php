@extends('layouts.app')

@section('title', 'Guestbook')

@section('content')
<div class="flex-between mb-2">
    <h1>Guestbook</h1>
    @auth
        <a href="{{ route('guestbook.create') }}" class="btn">Sign Guestbook</a>
    @endauth
</div>

@forelse($entries as $entry)
    <div class="card mb-2">
        <div class="flex-between">
            <div>
                <h3>{{ $entry->post->title }}</h3>
                <p class="text-muted">{{ $entry->post->user->first_name ?? 'Guest' }} • {{ $entry->post->created_at->diffForHumans() }}</p>
            </div>
            @if($entry->photo)
                <img src="{{ asset('storage/' . $entry->photo->path) }}" alt="Photo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
            @endif
        </div>
        <p style="margin-top: 1rem;">{{ $entry->post->description }}</p>
    </div>
@empty
    <div class="card">
        <p>No guestbook entries yet. Be the first to sign!</p>
    </div>
@endforelse

<div style="margin-top: 2rem;">
    {{ $entries->links() }}
</div>
@endsection
