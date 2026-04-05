@extends('layouts.app')

@section('title', 'Posts')

@section('content')
<div class="flex-between mb-2">
    <h1>Posts</h1>
    @auth
        <a href="{{ route('posts.create') }}" class="btn">Create Post</a>
    @endauth
</div>

@forelse($posts as $post)
    <div class="card mb-2">
        <h2>{{ $post->title }}</h2>
        <p class="text-muted">by {{ $post->user->first_name ?? 'Unknown' }} • {{ $post->created_at->diffForHumans() }}</p>
        <p style="margin-top: 1rem;">{{ Str::limit($post->description, 200) }}</p>
        <div class="flex" style="margin-top: 1rem;">
            <a href="{{ route('posts.show', $post) }}" class="btn">Read More</a>
            <span class="text-muted">👍 {{ $post->votes_count ?? 0 }} likes</span>
        </div>
    </div>
@empty
    <div class="card">
        <p>No posts yet. Share your thoughts!</p>
    </div>
@endforelse

<div style="margin-top: 2rem;">
    {{ $posts->links() }}
</div>
@endsection
