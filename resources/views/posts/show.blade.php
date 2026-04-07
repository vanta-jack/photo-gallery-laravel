@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('posts.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">
        Back to Posts
    </a>
</div>

<article class="bg-card text-card-foreground border border-border rounded p-6">
    <h1 class="text-2xl font-bold text-foreground">{{ $post->title }}</h1>
    <p class="text-sm text-muted-foreground mt-2">
        by {{ trim(($post->user?->first_name ?? '').' '.($post->user?->last_name ?? '')) !== '' ? trim($post->user?->first_name.' '.$post->user?->last_name) : 'Unknown' }}
        • {{ $post->created_at?->format('M d, Y') }}
    </p>
    <p class="text-foreground mt-5 whitespace-pre-wrap">{{ $post->description }}</p>

    <div class="mt-6 flex flex-wrap items-center gap-3">
        <span class="inline-flex items-center gap-1 text-sm text-muted-foreground">
            <x-icon name="thumbs-up" class="w-4 h-4" />
            {{ $post->votes->count() }} likes
        </span>
        @auth
            <form action="{{ route('posts.votes.store', $post) }}" method="POST">
                @csrf
                <button type="submit" class="bg-primary text-primary-foreground font-bold text-xs px-3 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                    Toggle Like
                </button>
            </form>
        @endauth
    </div>
</article>
@endsection
