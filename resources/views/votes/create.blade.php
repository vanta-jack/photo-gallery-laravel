@extends('layouts.app')

@section('title', 'Vote on Post')

@section('content')
@php
$postTitle = trim((string) ($post->title ?? ''));
if ($postTitle === '') {
    $postTitle = 'Untitled post';
}

$postDescriptionHtml = \App\Support\MarkdownRenderer::toSafeHtml($post->description);
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Vote on Post</h1>
                <p class="text-sm text-muted-foreground">Support content you enjoy with a quick vote.</p>
            </div>
        </x-slot:header>

        <x-ui.card padding="sm" class="space-y-2">
            <h2 class="text-base font-bold text-foreground">{{ $postTitle }}</h2>
            @if(filled($postDescriptionHtml))
                <x-ui.markdown-content :html="$postDescriptionHtml" class="text-muted-foreground" />
            @else
                <p class="text-sm text-muted-foreground">No post description provided.</p>
            @endif
        </x-ui.card>

        @if($existingVote)
            <x-ui.alert
                variant="muted"
                title="You already voted for this post."
                description="Use the controls below to keep or remove your vote."
            />
        @endif

        <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
            <form action="{{ route('posts.votes.store', $post) }}" method="POST">
                @csrf
                <x-ui.button type="submit">
                    {{ $existingVote ? 'Toggle vote' : 'Cast vote' }}
                </x-ui.button>
            </form>

            @if($existingVote)
                <form action="{{ route('posts.votes.destroy', [$post, $existingVote]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                        onclick="return confirm('Remove your vote?')"
                    >
                        Remove vote
                    </button>
                </form>
            @endif

            <a
                href="{{ route('posts.show', $post) }}"
                class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
            >
                Back to post
            </a>
        </div>
    </x-ui.card>
</div>
@endsection
