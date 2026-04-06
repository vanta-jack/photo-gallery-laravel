@extends('layouts.app')

@section('title', 'Sign Guestbook')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6">Sign Guestbook</h1>

    <form action="{{ route('guestbook.store') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label for="title" class="block text-sm font-bold mb-2 text-foreground">Name/Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">
            @error('title')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-bold mb-2 text-foreground">Message</label>
            <textarea id="description" name="description" rows="6" required class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">{{ old('description') }}</textarea>
            @error('description')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Sign Guestbook</button>
            <a href="{{ route('guestbook.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">Cancel</a>
        </div>
    </form>
</div>
@endsection
