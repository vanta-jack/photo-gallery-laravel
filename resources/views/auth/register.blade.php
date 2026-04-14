@extends('layouts.app')

@section('title', 'Register')

@section('content')

<div class="mx-auto max-w-2xl space-y-4">
    <x-ui.card class="space-y-4" aria-labelledby="register-heading">
        <x-slot:header>
            <div class="space-y-1">
                <h1 id="register-heading" class="text-2xl font-bold text-foreground">Create your account</h1>
                <p class="text-sm text-muted-foreground">Join VANITI FAIRE and start sharing your gallery activity.</p>
            </div>
        </x-slot:header>

        @if($errors->any())
            <x-ui.alert
                variant="destructive"
                title="Registration could not be completed."
                description="Please correct the highlighted fields and submit again."
            />
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.form-input
                    name="first_name"
                    label="First name"
                    required
                    autocomplete="given-name"
                    placeholder="First name"
                />

                <x-ui.form-input
                    name="last_name"
                    label="Last name"
                    required
                    autocomplete="family-name"
                    placeholder="Last name"
                />
            </div>

            <x-ui.form-input
                name="email"
                type="email"
                label="Email address"
                required
                autocomplete="email"
                placeholder="you@example.com"
            />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.form-input
                    name="password"
                    type="password"
                    label="Password"
                    required
                    autocomplete="new-password"
                    help="Use at least 8 characters."
                    placeholder="Create password"
                />

                <x-ui.form-input
                    name="password_confirmation"
                    type="password"
                    label="Confirm password"
                    required
                    autocomplete="new-password"
                    placeholder="Repeat password"
                />
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button type="submit">Create account</x-ui.button>
                <a
                    href="{{ route('login') }}"
                    class="text-sm font-bold text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80"
                >
                    Already registered? Sign in
                </a>
            </div>
        </form>
    </x-ui.card>
</div>

@endsection
