@extends('layouts.app')

@section('title', 'My Milestones')

@section('content')
@php
$stageLabels = \App\Models\Milestone::curatedStageOptions();
@endphp

<div class="space-y-6">
    <x-ui.card>
        <x-slot:header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">My Milestones</h1>
                    <p class="text-sm text-muted-foreground">Track key life stages and connect them to memorable photos.</p>
                </div>
                <x-ui.button as="a" href="{{ route('milestones.create') }}">Add milestone</x-ui.button>
            </div>
        </x-slot:header>

        @if($milestones->isEmpty())
            <x-ui.empty-state
                title="No milestones yet."
                description="Create your first milestone to start building your timeline."
                align="left"
            />
        @else
            <x-ui.pagination-shell :paginator="$milestones">
                <div class="space-y-3">
                    @foreach($milestones as $milestone)
                        @php
                        $photoPath = trim((string) ($milestone->photo?->path ?? ''));
                        $photoUrl = $photoPath === ''
                            ? null
                            : (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://') || str_starts_with($photoPath, '/')
                                ? $photoPath
                                : \Illuminate\Support\Facades\Storage::url($photoPath));

                        $stage = trim((string) ($milestone->stage ?? ''));
                        $stageLabel = $stageLabels[$stage] ?? ($stage !== '' ? $stage : 'Unspecified stage');
                        @endphp

                        <article class="rounded border border-border bg-background p-4 text-card-foreground">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h2 class="text-base font-bold text-foreground">{{ $milestone->label }}</h2>
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-ui.badge variant="muted" size="sm">{{ $stageLabel }}</x-ui.badge>
                                    <x-ui.badge variant="outline" size="sm">{{ $milestone->is_public ? 'Public' : 'Private' }}</x-ui.badge>
                                </div>
                            </div>

                            @if($photoUrl)
                                <div class="mt-3 overflow-hidden rounded border border-border bg-secondary">
                                    <img src="{{ $photoUrl }}" alt="{{ $milestone->label }}" class="h-44 w-full object-cover" loading="lazy">
                                </div>
                            @endif

                            <div class="mt-3">
                                @if(filled($milestone->description_html))
                                    <x-ui.markdown-content :html="$milestone->description_html" class="text-muted-foreground" />
                                @else
                                    <p class="text-sm text-muted-foreground">No milestone notes added yet.</p>
                                @endif
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-border pt-3">
                                <x-ui.button as="a" size="sm" variant="secondary" href="{{ route('milestones.show', $milestone) }}">View</x-ui.button>
                                <x-ui.button as="a" size="sm" variant="secondary" href="{{ route('milestones.edit', $milestone) }}">Edit</x-ui.button>
                                <form action="{{ route('milestones.destroy', $milestone) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" size="sm" variant="destructive" onclick="return confirm('Delete this milestone?')">Delete</x-ui.button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <x-slot:links>
                    {{ $milestones->onEachSide(1)->links() }}
                </x-slot:links>
            </x-ui.pagination-shell>
        @endif
    </x-ui.card>
</div>
@endsection
