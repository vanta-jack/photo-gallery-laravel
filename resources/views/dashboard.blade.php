@extends('layouts.app')

@section('title', 'Photo Gallery - Home')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-6 mb-8">
    <h1 class="text-3xl font-bold text-foreground mb-2">Welcome to Photo Gallery</h1>
    <p class="text-muted-foreground">A social photo sharing platform built with Laravel 13</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <div class="flex items-center gap-2 mb-2">
            <x-icon name="camera" class="w-5 h-5" />
            <h2 class="text-xl font-bold text-foreground">Photos</h2>
        </div>
        <p class="text-foreground mb-4">Browse and upload photos</p>
        <a href="{{ route('photos.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">View Photos</a>
    </div>

    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <div class="flex items-center gap-2 mb-2">
            <x-icon name="folder" class="w-5 h-5" />
            <h2 class="text-xl font-bold text-foreground">Albums</h2>
        </div>
        <p class="text-foreground mb-4">Organize photos into collections</p>
        <a href="{{ route('albums.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">View Albums</a>
    </div>

    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <div class="flex items-center gap-2 mb-2">
            <x-icon name="pen" class="w-5 h-5" />
            <h2 class="text-xl font-bold text-foreground">Posts</h2>
        </div>
        <p class="text-foreground mb-4">Share thoughts and updates</p>
        <a href="{{ route('posts.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">View Posts</a>
    </div>

    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <div class="flex items-center gap-2 mb-2">
            <x-icon name="pen-tool" class="w-5 h-5" />
            <h2 class="text-xl font-bold text-foreground">Guestbook</h2>
        </div>
        <p class="text-foreground mb-4">Leave a message for visitors</p>
        <a href="{{ route('guestbook.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">View Guestbook</a>
    </div>

    @auth
    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <div class="flex items-center gap-2 mb-2">
            <x-icon name="target" class="w-5 h-5" />
            <h2 class="text-xl font-bold text-foreground">Milestones</h2>
        </div>
        <p class="text-foreground mb-4">Track important life events</p>
        <a href="{{ route('milestones.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">View Milestones</a>
    </div>
    @endauth
</div>
@endsection
