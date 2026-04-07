@extends('layouts.app')

@section('title', $milestone->label)

@section('content')
<div class="mb-6 flex flex-wrap items-center gap-3">
    <a href="{{ route('milestones.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">
        Back to Milestones
    </a>
    <a href="{{ route('milestones.edit', $milestone) }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150 inline-block">
        Edit Milestone
    </a>
</div>

<article class="bg-card text-card-foreground border border-border rounded p-6">
    <div class="flex flex-wrap items-start justify-between gap-6">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-foreground">{{ ucfirst(str_replace('_', ' ', $milestone->stage)) }}: {{ $milestone->label }}</h1>
            <p class="text-sm text-muted-foreground mt-2">{{ $milestone->created_at->format('M d, Y') }}</p>
            <span class="inline-flex items-center gap-1 mt-2 text-xs rounded border border-border px-2 py-1 {{ $milestone->is_public ? 'bg-secondary text-secondary-foreground' : 'text-muted-foreground' }}">
                <x-icon name="{{ $milestone->is_public ? 'globe' : 'lock' }}" class="w-3 h-3" />
                {{ $milestone->is_public ? 'Public on Home' : 'Private' }}
            </span>
        </div>
        @if($milestone->photo)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($milestone->photo->path) }}" alt="Milestone photo" class="w-32 h-32 object-cover rounded">
        @endif
    </div>

    @if($milestone->description_html)
        <div class="mt-6 text-foreground text-sm leading-6 space-y-3">
            {!! $milestone->description_html !!}
        </div>
    @else
        <p class="mt-6 text-sm text-muted-foreground">No description has been added yet.</p>
    @endif
</article>
@endsection
