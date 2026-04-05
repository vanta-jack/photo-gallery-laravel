@extends('layouts.app')

@section('title', 'Upload Photo')

@section('content')
<div class="card">
    <h1>Upload Photo</h1>

    <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="photo">Photo File</label>
            <input type="file" id="photo" name="photo" accept="image/*" required>
            @error('photo')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
            @error('description')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="flex">
            <button type="submit" class="btn">Upload</button>
            <a href="{{ route('photos.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
