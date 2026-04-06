@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-8 max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6 text-center">Login to VANITI FAIRE</h1>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label for="email" class="block text-sm font-bold mb-2 text-foreground">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus
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
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" value="1" class="rounded border-input">
                <span class="text-sm text-foreground">Remember me</span>
            </label>
        </div>

        <div class="mb-6">
            <button type="submit" class="w-full bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                Login
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm text-muted-foreground">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-foreground font-bold hover:opacity-80 transition-opacity duration-150">Register</a>
            </p>
        </div>
    </form>
</div>
@endsection
