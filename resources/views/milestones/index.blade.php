@extends('layouts.app')

@section('title', 'Milestones')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-foreground">My Milestones</h1>
    <a href="{{ route('milestones.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Add Milestone</a>
</div>

@forelse($milestones as $milestone)
    <div class="bg-card text-card-foreground border border-border rounded p-6 mb-4">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h3 class="font-bold text-foreground text-lg mb-1">{{ ucfirst(str_replace('_', ' ', $milestone->stage)) }}: {{ $milestone->label }}</h3>
                <p class="text-muted-foreground text-sm mb-3">{{ $milestone->created_at->format('M d, Y') }}</p>
                <span class="inline-flex items-center gap-1 text-xs rounded border border-border px-2 py-1 mb-3 {{ $milestone->is_public ? 'bg-secondary text-secondary-foreground' : 'text-muted-foreground' }}">
                    <x-icon name="{{ $milestone->is_public ? 'globe' : 'lock' }}" class="w-3 h-3" />
                    {{ $milestone->is_public ? 'Public on Home' : 'Private' }}
                </span>
                @if($milestone->description_html)
                    <div class="text-foreground text-sm leading-6 space-y-3">
                        {!! $milestone->description_html !!}
                    </div>
                @endif
            </div>
            @if($milestone->photo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($milestone->photo->path) }}" alt="Milestone" class="w-24 h-24 object-cover rounded ml-4">
            @endif
        </div>
    </div>
@empty
    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <p class="text-foreground">No milestones yet. Start tracking life's important moments!</p>
    </div>
@endforelse

<div class="mt-8">
    {{ $milestones->links() }}
</div>
@endsection
