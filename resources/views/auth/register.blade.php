@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-8 max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6 text-center">Join VANITI FAIRE</h1>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label for="first_name" class="block text-sm font-bold mb-2 text-foreground">First Name</label>
            <input 
                type="text" 
                id="first_name" 
                name="first_name" 
                value="{{ old('first_name') }}" 
                required 
                autofocus
                class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                placeholder="John"
            >
            @error('first_name')
                <span class="text-destructive text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6">
            <label for="last_name" class="block text-sm font-bold mb-2 text-foreground">Last Name</label>
            <input 
                type="text" 
                id="last_name" 
                name="last_name" 
                value="{{ old('last_name') }}" 
                required
                class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                placeholder="Doe"
            >
            @error('last_name')
                <span class="text-destructive text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6">
            <label for="email" class="block text-sm font-bold mb-2 text-foreground">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required
                class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                placeholder="your@email.com"
            >
            @error('email')
                <span class="text-destructive text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-sm font-bold mb-2 text-foreground">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required
                class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                placeholder="••••••••"
            >
            @error('password')
                <span class="text-destructive text-sm mt-1 block">{{ $message }}</span>
            @enderror
            <p class="text-muted-foreground text-xs mt-1">Minimum 8 characters</p>
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-bold mb-2 text-foreground">Confirm Password</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required
                class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                placeholder="••••••••"
            >
        </div>

        <div class="mb-6">
            <button type="submit" class="w-full bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                Register
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm text-muted-foreground">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-foreground font-bold hover:opacity-80 transition-opacity duration-150">Login</a>
            </p>
        </div>
    </form>
</div>
@endsection
