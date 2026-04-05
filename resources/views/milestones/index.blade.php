@extends('layouts.app')

@section('title', 'Milestones')

@section('content')
<div class="flex-between mb-2">
    <h1>My Milestones</h1>
    <a href="{{ route('milestones.create') }}" class="btn">Add Milestone</a>
</div>

@forelse($milestones as $milestone)
    <div class="card mb-2">
        <div class="flex-between">
            <div>
                <h3>{{ ucfirst(str_replace('_', ' ', $milestone->stage)) }}: {{ $milestone->label }}</h3>
                <p class="text-muted">{{ $milestone->created_at->format('M d, Y') }}</p>
                @if($milestone->description)
                    <p style="margin-top: 0.5rem;">{{ $milestone->description }}</p>
                @endif
            </div>
            @if($milestone->photo)
                <img src="{{ asset('storage/' . $milestone->photo->path) }}" alt="Milestone" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
            @endif
        </div>
    </div>
@empty
    <div class="card">
        <p>No milestones yet. Start tracking life's important moments!</p>
    </div>
@endforelse

<div style="margin-top: 2rem;">
    {{ $milestones->links() }}
</div>
@endsection
