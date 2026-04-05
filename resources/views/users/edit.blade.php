@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="card">
    <h1>Edit Profile</h1>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}">
            @error('first_name')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
            @error('last_name')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
            @error('email')<span style="color: #e74c3c;">{{ $message }}</span>@enderror
        </div>

        <div class="flex">
            <button type="submit" class="btn">Update Profile</button>
        </div>
    </form>
</div>
@endsection
