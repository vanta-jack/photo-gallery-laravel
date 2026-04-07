@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-foreground inline-flex items-center gap-2">
            <x-icon name="grid" class="w-6 h-6" />
            Admin Dashboard
        </h1>
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
            <div class="h-40 border border-border rounded p-2 mb-4 bg-background/40">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full">
                    <polyline
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="text-primary"
                        points="@foreach($registrationChart as $point){{ $point['x'] }},{{ $point['y'] }} @endforeach"
                    ></polyline>
                </svg>
            </div>
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
            <div class="h-40 border border-border rounded p-2 mb-4 bg-background/40">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full">
                    <polyline
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="text-secondary-foreground"
                        points="@foreach($sessionChart as $point){{ $point['x'] }},{{ $point['y'] }} @endforeach"
                    ></polyline>
                </svg>
            </div>
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

    <section class="bg-card border border-border rounded p-6">
        <h2 class="text-lg font-bold text-foreground mb-4 inline-flex items-center gap-2">
            <x-icon name="eye" class="w-5 h-5" />
            Accounts
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted-foreground border-b border-border">
                        <th class="py-2">Name</th>
                        <th class="py-2">Email</th>
                        <th class="py-2">Role</th>
                        <th class="py-2">Joined</th>
                        <th class="py-2">Profile</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                        <tr class="border-b border-border/50">
                            <td class="py-2 font-medium text-foreground">{{ trim(($account->first_name ?? '').' '.($account->last_name ?? '')) ?: 'User #'.$account->id }}</td>
                            <td class="py-2 text-muted-foreground">{{ $account->email ?? '—' }}</td>
                            <td class="py-2 text-foreground">{{ ucfirst($account->role) }}</td>
                            <td class="py-2 text-muted-foreground">{{ $account->created_at?->format('M d, Y') }}</td>
                            <td class="py-2">
                                <a href="{{ route('users.show', $account) }}" class="text-primary font-bold hover:underline">Preview</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2">
        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4 inline-flex items-center gap-2">
                <x-icon name="alert" class="w-5 h-5" />
                Moderate Posts
            </h2>
            <div class="space-y-3">
                @foreach($moderation['posts'] as $post)
                    <div class="border border-border rounded p-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-bold text-foreground">{{ $post->title }}</p>
                            <p class="text-xs text-muted-foreground">{{ $post->created_at?->format('M d, Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-destructive text-destructive-foreground font-bold text-xs px-3 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </article>
        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4 inline-flex items-center gap-2">
                <x-icon name="alert" class="w-5 h-5" />
                Moderate Photos
            </h2>
            <div class="space-y-3">
                @foreach($moderation['photos'] as $photo)
                    <div class="border border-border rounded p-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-bold text-foreground">{{ $photo->title }}</p>
                            <p class="text-xs text-muted-foreground">{{ $photo->created_at?->format('M d, Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-destructive text-destructive-foreground font-bold text-xs px-3 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </article>
        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4 inline-flex items-center gap-2">
                <x-icon name="alert" class="w-5 h-5" />
                Moderate Albums
            </h2>
            <div class="space-y-3">
                @foreach($moderation['albums'] as $album)
                    <div class="border border-border rounded p-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-bold text-foreground">{{ $album->title }}</p>
                            <p class="text-xs text-muted-foreground">{{ $album->created_at?->format('M d, Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.albums.destroy', $album) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-destructive text-destructive-foreground font-bold text-xs px-3 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </article>
        <article class="bg-card border border-border rounded p-6">
            <h2 class="text-lg font-bold text-foreground mb-4 inline-flex items-center gap-2">
                <x-icon name="alert" class="w-5 h-5" />
                Moderate Milestones
            </h2>
            <div class="space-y-3">
                @foreach($moderation['milestones'] as $milestone)
                    <div class="border border-border rounded p-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-bold text-foreground">{{ $milestone->label }}</p>
                            <p class="text-xs text-muted-foreground">{{ $milestone->created_at?->format('M d, Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.milestones.destroy', $milestone) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-destructive text-destructive-foreground font-bold text-xs px-3 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="bg-card border border-border rounded p-6">
        <h2 class="text-lg font-bold text-foreground mb-4 inline-flex items-center gap-2">
            <x-icon name="alert" class="w-5 h-5" />
            Moderate Guestbook Entries
        </h2>
        <div class="space-y-3">
            @foreach($moderation['guestbook'] as $entry)
                <div class="border border-border rounded p-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-foreground">{{ $entry->post?->title ?? 'Guestbook entry' }}</p>
                        <p class="text-xs text-muted-foreground">{{ $entry->created_at?->format('M d, Y') }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.guestbook.destroy', $entry) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-destructive text-destructive-foreground font-bold text-xs px-3 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
