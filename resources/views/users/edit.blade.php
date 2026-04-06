@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6">Edit Profile</h1>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="first_name" class="block text-sm font-bold mb-2 text-foreground">First Name</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">
            @error('first_name')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <label for="last_name" class="block text-sm font-bold mb-2 text-foreground">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">
            @error('last_name')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <label for="email" class="block text-sm font-bold mb-2 text-foreground">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">
            @error('email')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Update Profile</button>
        </div>
    </form>
</div>
@endsection
