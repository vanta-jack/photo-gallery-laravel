@extends('layouts.app')

@section('title', 'Login')

@section('content')

<div class="mx-auto max-w-xl space-y-4">
    <x-ui.card class="space-y-4" aria-labelledby="login-heading">
        <x-slot:header>
            <div class="space-y-1">
                <h1 id="login-heading" class="text-2xl font-bold text-foreground">Welcome back</h1>
                <p class="text-sm text-muted-foreground">Sign in to access your personal gallery tools.</p>
            </div>
        </x-slot:header>

        @if($errors->any())
            <x-ui.alert
                variant="destructive"
                title="Unable to sign in."
                description="Please review the highlighted fields and try again."
            />
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <x-ui.form-input
                name="email"
                type="email"
                label="Email address"
                required
                autocomplete="email"
                placeholder="you@example.com"
            />

            <x-ui.form-input
                name="password"
                type="password"
                label="Password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
            />

            <div class="flex items-center gap-2">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    value="1"
                    @checked(old('remember'))
                    class="h-4 w-4 rounded border border-input bg-background text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
                >
                <label for="remember" class="text-sm text-foreground">Remember me on this device</label>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button type="submit">Sign in</x-ui.button>
                <a
                    href="{{ route('register') }}"
                    class="text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80"
                >
                    Need an account? Register
                </a>
            </div>
        </form>
    </x-ui.card>
</div>

@endsection
