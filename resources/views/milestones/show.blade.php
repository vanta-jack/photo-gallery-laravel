@extends('layouts.app')

@section('title', 'Milestone Details')

@section('content')
@php
$stageLabels = \App\Models\Milestone::curatedStageOptions();
$stage = trim((string) ($milestone->stage ?? ''));
$stageLabel = $stageLabels[$stage] ?? ($stage !== '' ? $stage : 'Unspecified stage');
$photoPath = trim((string) ($milestone->photo?->path ?? ''));
$photoUrl = $photoPath === ''
    ? null
    : (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://') || str_starts_with($photoPath, '/')
        ? $photoPath
        : \Illuminate\Support\Facades\Storage::url($photoPath));
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-5">
        <x-slot:header>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold text-foreground">{{ $milestone->label }}</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ $milestone->created_at?->format('M j, Y') ? 'Created '.$milestone->created_at->format('M j, Y') : 'Creation date unavailable' }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge variant="muted" size="sm">{{ $stageLabel }}</x-ui.badge>
                    <x-ui.badge variant="outline" size="sm">{{ $milestone->is_public ? 'Public' : 'Private' }}</x-ui.badge>
                </div>
            </div>
        </x-slot:header>

        @if($photoUrl)
            <div class="overflow-hidden rounded border border-border bg-secondary mx-auto max-w-2xl">
                <img src="{{ $photoUrl }}" alt="{{ $milestone->label }}" class="max-h-[30rem] w-full object-contain" loading="lazy">
            </div>
        @endif

        @include('photos.partials.slideshow-modal', [
            'photos' => $spotlightPhotos,
            'triggerLabel' => 'View photos',
            'rootId' => 'milestone-spotlight-modal',
            'showTrigger' => $spotlightPhotos->isNotEmpty(),
        ])

        @if(filled($milestone->description_html))
            <x-ui.markdown-content :html="$milestone->description_html" class="max-w-2xl mx-auto" />
        @else
            <x-ui.empty-state
                title="No milestone notes yet."
                description="Add details to describe why this milestone matters."
                compact
                align="left"
            />
        @endif

        <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
            @can('update', $milestone)
                <x-ui.button as="a" variant="secondary" href="{{ route('milestones.edit', $milestone) }}">Edit</x-ui.button>
            @endcan

            @can('delete', $milestone)
                <form action="{{ route('milestones.destroy', $milestone) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="destructive" onclick="return confirm('Delete this milestone?')">Delete</x-ui.button>
                </form>
            @endcan

            <x-ui.button as="a" variant="secondary" href="{{ route('milestones.index') }}">Back to milestones</x-ui.button>
        </div>
    </x-ui.card>
</div>
@endsection
