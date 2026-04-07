@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-foreground">Admin Dashboard</h1>
        <p class="text-sm text-muted-foreground mt-1">Operational metrics for users, sessions, and activity.</p>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article class="bg-card border border-border rounded p-4">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Online Users (15m)</p>
            <p class="mt-2 text-2xl font-bold text-foreground">{{ $liveSessions['online_users'] }}</p>
        </article>
        <article class="bg-card border border-border rounded p-4">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Concurrent Sessions</p>
            <p class="mt-2 text-2xl font-bold text-foreground">{{ $liveSessions['concurrent_sessions'] }}</p>
        </article>
        <article class="bg-card border border-border rounded p-4">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Guest Sessions</p>
            <p class="mt-2 text-2xl font-bold text-foreground">{{ $liveSessions['guest_sessions'] }}</p>
        </article>
        <article class="bg-card border border-border rounded p-4">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Total Users</p>
            <p class="mt-2 text-2xl font-bold text-foreground">{{ $roleBreakdown['total_users'] }}</p>
        </article>
    </section>

    <section class="grid gap-4 md:grid-cols-2">
        <article class="bg-card border border-border rounded p-6 space-y-4">
            <h2 class="text-lg font-bold text-foreground">Role Breakdown</h2>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Admins</span>
                    <span class="font-bold text-foreground">{{ $roleBreakdown['admin_users'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Users</span>
                    <span class="font-bold text-foreground">{{ $roleBreakdown['regular_users'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Guests</span>
                    <span class="font-bold text-foreground">{{ $roleBreakdown['guest_users'] }}</span>
                </div>
            </div>
        </article>

        <article class="bg-card border border-border rounded p-6 space-y-4">
            <h2 class="text-lg font-bold text-foreground">Content Totals</h2>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Photos</span>
                    <span class="font-bold text-foreground">{{ $contentTotals['photos'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Albums</span>
                    <span class="font-bold text-foreground">{{ $contentTotals['albums'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Posts</span>
                    <span class="font-bold text-foreground">{{ $contentTotals['posts'] }}</span>
                </div>
            </div>
        </article>
    </section>

    <section class="grid gap-4 md:grid-cols-2">
        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4">Registrations (14 days)</h2>
            <div class="space-y-2">
                @foreach($registrations as $point)
                    <div class="border border-border rounded p-3">
                        <div class="flex justify-between text-xs">
                            <span class="text-muted-foreground">{{ $point['label'] }}</span>
                            <span class="font-medium text-foreground">{{ $point['total'] }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded bg-muted">
                            <div class="h-2 rounded bg-primary" style="width: {{ $point['intensity'] }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4">Session Traffic (14 days)</h2>
            <div class="space-y-2">
                @foreach($sessionTraffic as $point)
                    <div class="border border-border rounded p-3">
                        <div class="flex justify-between text-xs">
                            <span class="text-muted-foreground">{{ $point['label'] }}</span>
                            <span class="font-medium text-foreground">{{ $point['total'] }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded bg-muted">
                            <div class="h-2 rounded bg-secondary" style="width: {{ $point['intensity'] }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
</div>
@endsection
