@extends('layouts.app')

@section('title', 'Posts')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-foreground inline-flex items-center gap-2">
        <x-icon name="pen" class="w-6 h-6" />
        My Posts
    </h1>
    @auth
        <a href="{{ route('posts.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Create Post</a>
    @endauth
</div>

@forelse($posts as $post)
    <div class="bg-card text-card-foreground border border-border rounded p-6 mb-4">
        <h2 class="text-xl font-bold text-foreground mb-2">{{ $post->title }}</h2>
        <div class="mb-4 flex items-center gap-3">
            @include('guestbook.partials.user-avatar', ['user' => $post->user])
            <p class="text-muted-foreground text-sm">by {{ trim(($post->user?->first_name ?? '').' '.($post->user?->last_name ?? '')) !== '' ? trim(($post->user?->first_name ?? '').' '.($post->user?->last_name ?? '')) : 'Unknown' }} • {{ $post->created_at->diffForHumans() }}</p>
        </div>
        @if($post->description_html)
            <div class="text-foreground mb-4 text-sm leading-6">{!! $post->description_html !!}</div>
        @endif
        <div class="flex items-center gap-4">
            <a href="{{ route('posts.show', $post) }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">Read More</a>
            <span class="text-muted-foreground text-sm flex items-center gap-1">
                <x-icon name="thumbs-up" class="w-4 h-4" />
                {{ $post->votes_count ?? 0 }} likes
            </span>
        </div>
    </div>
@empty
    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <p class="text-foreground">No posts yet. Share your thoughts!</p>
    </div>
@endforelse

<div class="mt-8">
    {{ $posts->links() }}
</div>
@endsection
