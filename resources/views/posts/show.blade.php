@extends('layouts.app')

@section('title', 'Post Details')

@section('content')
@php
$title = trim((string) ($post->title ?? ''));
if ($title === '') {
    $title = 'Untitled post';
}
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-4">
        <x-slot:header>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold text-foreground">{{ $title }}</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ $post->created_at?->format('M j, Y') ? 'Published '.$post->created_at->format('M j, Y') : 'Publication date unavailable' }}
                    </p>
                </div>
                <x-ui.badge variant="muted" size="sm">{{ (int) $post->votes->count() }} votes</x-ui.badge>
            </div>
        </x-slot:header>

        @include('guestbook.partials.user-avatar', [
            'user' => $post->user,
            'meta' => 'Author',
        ])

        @if($mainPhoto?->path)
            @php
            $postPhotoPath = trim((string) $mainPhoto->path);
            $postPhotoUrl = str_starts_with($postPhotoPath, 'http://') || str_starts_with($postPhotoPath, 'https://') || str_starts_with($postPhotoPath, '/')
                ? $postPhotoPath
                : \Illuminate\Support\Facades\Storage::url($postPhotoPath);
            @endphp
            <div class="overflow-hidden rounded border border-border bg-secondary mx-auto max-w-2xl">
                <img src="{{ $postPhotoUrl }}" alt="{{ $post->title }} main photo" class="max-h-[28rem] w-full object-cover" loading="lazy">
            </div>
        @endif

        @if(($attachmentPhotos ?? collect())->isNotEmpty())
            <section class="space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-sm font-bold text-foreground">Attached photos</h2>
                    @include('photos.partials.slideshow-modal', [
                        'photos' => $attachmentPhotos,
                        'triggerLabel' => 'View photos',
                        'rootId' => 'post-spotlight-modal',
                        'showTrigger' => true,
                    ])
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($attachmentPhotos as $photo)
                        @php
                        $path = trim((string) ($photo->path ?? ''));
                        $url = $path === ''
                            ? null
                            : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
                                ? $path
                                : \Illuminate\Support\Facades\Storage::url($path));
                        @endphp
                        @continue($url === null)

                        <figure class="space-y-2 rounded border border-border bg-card p-2">
                            <img
                                src="{{ $url }}"
                                alt="{{ $photo->title ?: $post->title.' attachment' }}"
                                class="h-32 w-full rounded object-cover"
                                loading="lazy"
                            >
                            @if($mainPhoto && (int) $mainPhoto->id === (int) $photo->id)
                                <x-ui.badge size="sm" variant="secondary">Main image</x-ui.badge>
                            @endif
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        @if(filled($post->description_html))
            <x-ui.markdown-content :html="$post->description_html" class="max-w-2xl mx-auto" />
        @else
            <x-ui.empty-state
                title="No post content yet."
                description="This post does not include any markdown content."
                compact
                align="left"
            />
        @endif

        <div class="space-y-2 border-t border-border pt-4">
            <h2 class="text-sm font-bold text-foreground">Recent voters</h2>
            @if($post->votes->isEmpty())
                <p class="text-sm text-muted-foreground">No votes recorded yet.</p>
            @else
                <ul class="space-y-1 text-sm text-muted-foreground">
                    @foreach($post->votes->take(8) as $vote)
                        @php
                        $voterName = trim(sprintf('%s %s', (string) ($vote->user?->first_name ?? ''), (string) ($vote->user?->last_name ?? '')));
                        if ($voterName === '') {
                            $voterName = 'Anonymous voter';
                        }
                        @endphp
                        <li>{{ $voterName }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
            @auth
                <x-ui.button as="a" variant="secondary" href="{{ route('posts.votes.create', $post) }}">Vote on this post</x-ui.button>
            @endauth

            @can('update', $post)
                <x-ui.button as="a" variant="secondary" href="{{ route('posts.edit', $post) }}">Edit</x-ui.button>
            @endcan

            @can('delete', $post)
                <form action="{{ route('posts.destroy', $post) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="destructive" onclick="return confirm('Delete this post?')">Delete</x-ui.button>
                </form>
            @endcan

            <x-ui.button as="a" variant="secondary" href="{{ route('posts.index') }}">Back to posts</x-ui.button>
        </div>
    </x-ui.card>
</div>
@endsection
