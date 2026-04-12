@extends('layouts.app')

@section('title', 'Page')

@section('content')

<div class="space-y-4">
    <div class="bg-card text-card-foreground border border-border rounded p-6">
        <h1 class="text-2xl font-bold text-foreground mb-4">Page Title</h1>
        <p class="text-muted-foreground">Your blank canvas for this page.</p>
        <p class="text-sm text-muted-foreground mt-4">Edit this view at: <code class="bg-secondary px-2 py-1 rounded">resources/views/path/to/view.blade.php</code></p>
    </div>
</div>

@endsection
