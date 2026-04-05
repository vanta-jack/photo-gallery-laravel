@extends('layouts.app')

@section('title', 'Create Album')

@section('content')
<div class="card">
    <h1>Create Album</h1>

    <form action="{{ route('albums.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Album Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
            @error('description')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_private" value="1" {{ old('is_private') ? 'checked' : '' }}>
                Make this album private
            </label>
        </div>

        <div class="flex">
            <button type="submit" class="btn">Create Album</button>
            <a href="{{ route('albums.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
