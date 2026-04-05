@extends('layouts.app')

@section('title', 'Sign Guestbook')

@section('content')
<div class="card">
    <h1>Sign Guestbook</h1>

    <form action="{{ route('guestbook.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Name/Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="description">Message</label>
            <textarea id="description" name="description" required>{{ old('description') }}</textarea>
            @error('description')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="flex">
            <button type="submit" class="btn">Sign Guestbook</button>
            <a href="{{ route('guestbook.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
