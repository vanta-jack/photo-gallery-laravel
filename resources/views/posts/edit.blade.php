@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6 inline-flex items-center gap-2">
        <x-icon name="edit" class="w-6 h-6" />
        Edit Post
    </h1>

    <form action="{{ route('posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="title" class="block text-sm font-bold mb-2 text-foreground">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">
            @error('title')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-bold mb-2 text-foreground">Content (Markdown supported)</label>
            <textarea id="description" name="description" rows="10" required data-markdown-editor class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">{{ old('description', $post->description) }}</textarea>
            @error('description')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Update Post</button>
            <a href="{{ route('posts.show', $post) }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">Cancel</a>
        </div>
    </form>
</div>
@endsection
