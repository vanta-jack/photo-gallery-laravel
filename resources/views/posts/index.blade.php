@extends('layouts.app')

@section('title', 'Posts')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-foreground">Posts</h1>
    @auth
        <a href="{{ route('posts.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Create Post</a>
    @endauth
</div>

@forelse($posts as $post)
    <div class="bg-card text-card-foreground border border-border rounded p-6 mb-4">
        <h2 class="text-xl font-bold text-foreground mb-2">{{ $post->title }}</h2>
        <p class="text-muted-foreground text-sm mb-4">by {{ $post->user->first_name ?? 'Unknown' }} • {{ $post->created_at->diffForHumans() }}</p>
        <p class="text-foreground mb-4">{{ Str::limit($post->description, 200) }}</p>
        <div class="flex items-center gap-4">
            <a href="{{ route('posts.show', $post) }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">Read More</a>
            <span class="text-muted-foreground text-sm">👍 {{ $post->votes_count ?? 0 }} likes</span>
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
