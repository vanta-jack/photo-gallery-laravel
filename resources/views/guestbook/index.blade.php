@extends('layouts.app')

@section('title', 'Guestbook')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-foreground">Guestbook Feed</h1>
        <p class="text-sm text-muted-foreground mt-1">Latest guestbook entries with photo and engagement context.</p>
    </div>
    @auth
        <a href="{{ route('guestbook.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Sign Guestbook</a>
    @endauth
</div>

@forelse($entries as $entry)
    @include('guestbook.partials.feed-item', ['entry' => $entry])
@empty
    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <p class="text-foreground">No guestbook entries yet. Be the first to sign!</p>
    </div>
@endforelse

<div class="mt-8">
    {{ $entries->links() }}
</div>
@endsection
