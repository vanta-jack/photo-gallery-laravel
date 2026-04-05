@extends('layouts.app')

@section('title', 'Photo Gallery - Home')

@section('content')
<div class="card mb-2">
    <h1>Welcome to Photo Gallery</h1>
    <p class="text-muted">A social photo sharing platform built with Laravel 13</p>
</div>

<div class="grid grid-2">
    <div class="card">
        <h2>📸 Photos</h2>
        <p>Browse and upload photos</p>
        <a href="{{ route('photos.index') }}" class="btn">View Photos</a>
    </div>

    <div class="card">
        <h2>📁 Albums</h2>
        <p>Organize photos into collections</p>
        <a href="{{ route('albums.index') }}" class="btn">View Albums</a>
    </div>

    <div class="card">
        <h2>📝 Posts</h2>
        <p>Share thoughts and updates</p>
        <a href="{{ route('posts.index') }}" class="btn">View Posts</a>
    </div>

    <div class="card">
        <h2>✍️ Guestbook</h2>
        <p>Leave a message for visitors</p>
        <a href="{{ route('guestbook.index') }}" class="btn">View Guestbook</a>
    </div>

    @auth
    <div class="card">
        <h2>🎯 Milestones</h2>
        <p>Track important life events</p>
        <a href="{{ route('milestones.index') }}" class="btn">View Milestones</a>
    </div>
    @endauth
</div>
@endsection
