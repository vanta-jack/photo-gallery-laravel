@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
<div class="card">
    <h1>Create Post</h1>

    <form action="{{ route('posts.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="description">Content (Markdown supported)</label>
            <textarea id="description" name="description" style="min-height: 200px;" required>{{ old('description') }}</textarea>
            @error('description')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="flex">
            <button type="submit" class="btn">Publish Post</button>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
