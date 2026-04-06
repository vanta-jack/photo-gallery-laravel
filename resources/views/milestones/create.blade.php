@extends('layouts.app')

@section('title', 'Create Milestone')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6">Create Milestone</h1>

    <form action="{{ route('milestones.store') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label for="stage" class="block text-sm font-bold mb-2 text-foreground">Life Stage</label>
            <select id="stage" name="stage" required class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                <option value="">Select stage...</option>
                <option value="baby" {{ old('stage') === 'baby' ? 'selected' : '' }}>Baby</option>
                <option value="grade_school" {{ old('stage') === 'grade_school' ? 'selected' : '' }}>Grade School</option>
                <option value="highschool_college" {{ old('stage') === 'highschool_college' ? 'selected' : '' }}>High School / College</option>
            </select>
            @error('stage')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <label for="label" class="block text-sm font-bold mb-2 text-foreground">Label (e.g., "Month 3", "Grade 2")</label>
            <input type="text" id="label" name="label" value="{{ old('label') }}" required class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">
            @error('label')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-bold mb-2 text-foreground">Description</label>
            <textarea id="description" name="description" rows="4" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">{{ old('description') }}</textarea>
            @error('description')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Create Milestone</button>
            <a href="{{ route('milestones.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">Cancel</a>
        </div>
    </form>
</div>
@endsection
