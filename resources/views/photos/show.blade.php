@extends('layouts.app')

@section('title', $photo->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('photos.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">
        Back to Photos
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-card text-card-foreground border border-border rounded p-4">
            <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" class="w-full max-h-[70vh] object-contain rounded border border-border bg-secondary">
        </div>
    </div>

    <aside class="space-y-4">
        <div class="bg-card text-card-foreground border border-border rounded p-4">
            <h1 class="text-2xl font-bold text-foreground mb-3">{{ $photo->title }}</h1>
            @if($photo->description)
                <p class="text-foreground mb-4">{{ $photo->description }}</p>
            @endif

            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Uploaded by</dt>
                    <dd class="text-foreground font-bold">{{ $photo->user?->first_name }} {{ $photo->user?->last_name }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Uploaded</dt>
                    <dd class="text-foreground font-bold">{{ $photo->created_at->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Average rating</dt>
                    <dd class="text-foreground font-bold">{{ number_format($photo->ratings->avg('rating') ?? 0, 1) }}/5</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Ratings</dt>
                    <dd class="text-foreground font-bold">{{ $photo->ratings->count() }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Comments</dt>
                    <dd class="text-foreground font-bold">{{ $photo->comments->count() }}</dd>
                </div>
            </dl>
        </div>

        @auth
            <div class="bg-card text-card-foreground border border-border rounded p-4 space-y-4">
                <form action="{{ route('photos.ratings.store', $photo) }}" method="POST" class="space-y-2">
                    @csrf
                    <label for="rating" class="block text-sm font-bold text-foreground">Your Rating</label>
                    <select id="rating" name="rating" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very Good</option>
                        <option value="3">3 - Good</option>
                        <option value="2">2 - Fair</option>
                        <option value="1">1 - Poor</option>
                    </select>
                    <button type="submit" class="w-full bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                        Save Rating
                    </button>
                </form>

                <form action="{{ route('photos.comments.store', $photo) }}" method="POST" class="space-y-2">
                    @csrf
                    <label for="content" class="block text-sm font-bold text-foreground">Add Comment</label>
                    <textarea id="content" name="content" rows="3" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"></textarea>
                    <button type="submit" class="w-full bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">
                        Post Comment
                    </button>
                </form>

                @can('update', $photo)
                    <a href="{{ route('photos.edit', $photo) }}" class="w-full bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block text-center">
                        Edit Photo
                    </a>
                @endcan

                @can('delete', $photo)
                    <form action="{{ route('photos.destroy', $photo) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this photo? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-destructive text-white font-bold text-sm px-4 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150">
                            Delete Photo
                        </button>
                    </form>
                @endcan
            </div>
        @endauth
    </aside>
</div>

<div class="mt-6 bg-card text-card-foreground border border-border rounded p-4">
    <h2 class="text-lg font-bold text-foreground mb-4">Comments</h2>
    @forelse($photo->comments as $comment)
        <article class="py-3 border-b border-border last:border-b-0">
            <p class="text-foreground text-sm">{{ $comment->content }}</p>
            <p class="text-muted-foreground text-xs mt-2">
                {{ $comment->user?->first_name }} {{ $comment->user?->last_name }} • {{ $comment->created_at->diffForHumans() }}
            </p>
        </article>
    @empty
        <p class="text-muted-foreground text-sm">No comments yet.</p>
    @endforelse
</div>
@endsection
