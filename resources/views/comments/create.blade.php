@extends('layouts.app')

@section('title', 'Create Comment')

@section('content')
@php
$photoTitle = trim((string) ($photo->title ?? ''));
if ($photoTitle === '') {
    $photoTitle = 'Untitled photo';
}

$path = trim((string) ($photo->path ?? ''));
$photoUrl = $path === ''
    ? null
    : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
        ? $path
        : \Illuminate\Support\Facades\Storage::url($path));
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-foreground">Add Comment</h1>
                <p class="text-sm text-muted-foreground">Share feedback for <span class="font-bold text-foreground">{{ $photoTitle }}</span>.</p>
            </div>
        </x-slot:header>

        @if($photoUrl)
            <div class="overflow-hidden rounded border border-border bg-secondary">
                <img src="{{ $photoUrl }}" alt="{{ $photoTitle }}" class="max-h-80 w-full object-cover" loading="lazy">
            </div>
        @endif

        <form action="{{ route('photos.comments.store', $photo) }}" method="POST" class="space-y-5">
            @csrf

            <x-ui.form-textarea
                name="body"
                label="Comment"
                :value="old('body')"
                rows="5"
                placeholder="What do you notice about this photo?"
                required
            />

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit">Post comment</x-ui.button>

                <a
                    href="{{ route('photos.show', $photo) }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Cancel
                </a>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
