@extends('layouts.app')

@section('title', 'Create Milestone')

@section('content')
<div class="card">
    <h1>Create Milestone</h1>

    <form action="{{ route('milestones.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="stage">Life Stage</label>
            <select id="stage" name="stage" required>
                <option value="">Select stage...</option>
                <option value="baby" {{ old('stage') === 'baby' ? 'selected' : '' }}>Baby</option>
                <option value="grade_school" {{ old('stage') === 'grade_school' ? 'selected' : '' }}>Grade School</option>
                <option value="highschool_college" {{ old('stage') === 'highschool_college' ? 'selected' : '' }}>High School / College</option>
            </select>
            @error('stage')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="label">Label (e.g., "Month 3", "Grade 2")</label>
            <input type="text" id="label" name="label" value="{{ old('label') }}" required>
            @error('label')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
            @error('description')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="flex">
            <button type="submit" class="btn">Create Milestone</button>
            <a href="{{ route('milestones.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
