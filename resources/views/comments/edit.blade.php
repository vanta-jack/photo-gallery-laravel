@extends('layouts.app')

@section('title', 'Edit Comment')

@section('content')
@php
$photoTitle = trim((string) ($photo->title ?? ''));
if ($photoTitle === '') {
    $photoTitle = 'Untitled photo';
}
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Edit Comment</h1>
                <p class="text-sm text-muted-foreground">Update your comment on <span class="font-bold text-foreground">{{ $photoTitle }}</span>.</p>
            </div>
        </x-slot:header>

        <form action="{{ route('photos.comments.update', [$photo, $comment]) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <x-ui.form-textarea
                name="body"
                label="Comment"
                :value="old('body', $comment->body)"
                rows="5"
                required
            />

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit">Save comment</x-ui.button>

                <a
                    href="{{ route('photos.show', $photo) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Cancel
                </a>
            </div>
        </form>

        <form action="{{ route('photos.comments.destroy', [$photo, $comment]) }}" method="POST" class="border-t border-border pt-4">
            @csrf
            @method('DELETE')
            <button
                type="submit"
                class="inline-flex items-center rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive transition-opacity duration-150 hover:opacity-80"
                onclick="return confirm('Delete this comment?')"
            >
                Delete comment
            </button>
        </form>
    </x-ui.card>
</div>
@endsection
