<!DOCTYPE html>
<html lang="en" class="{{ session('theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VANITI FAIRE — @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground font-sans antialiased min-h-screen">

    @include('layouts.partials.header')

    {{-- Flash messages --}}
    @if(session('status'))
        <div class="max-w-5xl mx-auto px-4 mt-4">
            <div class="bg-card border border-border rounded p-3 text-sm">
                {{ session('status') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-5xl mx-auto px-4 mt-4">
            <div class="bg-card border border-border rounded p-3 text-sm text-destructive">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="max-w-5xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

</body>
</html>
