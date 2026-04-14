@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
@php
$liveSessions = is_array($liveSessions ?? null) ? $liveSessions : [];
$roleBreakdown = is_array($roleBreakdown ?? null) ? $roleBreakdown : [];
$contentTotals = is_array($contentTotals ?? null) ? $contentTotals : [];

$registrationSeries = collect($registrations ?? [])->filter(fn ($point): bool => is_array($point))->values();
$sessionSeries = collect($sessionTraffic ?? [])->filter(fn ($point): bool => is_array($point))->values();
$registrationChartSeries = collect($registrationChart ?? [])->filter(fn ($point): bool => is_array($point))->values();
$sessionChartSeries = collect($sessionChart ?? [])->filter(fn ($point): bool => is_array($point))->values();

$registrationPolyline = $registrationChartSeries
    ->map(fn ($point): string => sprintf('%s,%s', (float) ($point['x'] ?? 0), (float) ($point['y'] ?? 100)))
    ->implode(' ');
$sessionPolyline = $sessionChartSeries
    ->map(fn ($point): string => sprintf('%s,%s', (float) ($point['x'] ?? 0), (float) ($point['y'] ?? 100)))
    ->implode(' ');

$accounts = collect($accounts ?? []);
$moderation = is_array($moderation ?? null) ? $moderation : [];
$moderationPosts = collect($moderation['posts'] ?? []);
$moderationPhotos = collect($moderation['photos'] ?? []);
$moderationAlbums = collect($moderation['albums'] ?? []);
$moderationMilestones = collect($moderation['milestones'] ?? []);
$moderationGuestbook = collect($moderation['guestbook'] ?? []);

$formatUserName = static function ($user): string {
    if ($user === null) {
        return 'Unknown user';
    }

    $name = trim(sprintf('%s %s', (string) ($user->first_name ?? ''), (string) ($user->last_name ?? '')));

    if ($name !== '') {
        return $name;
    }

    return isset($user->id) ? 'User #'.$user->id : 'Unknown user';
};

$formatDate = static function ($value): string {
    if ($value === null) {
        return '—';
    }

    if ($value instanceof \Carbon\CarbonInterface) {
        return $value->format('Y-m-d H:i');
    }

    if (is_string($value) && trim($value) !== '') {
        try {
            return \Illuminate\Support\Carbon::parse($value)->format('Y-m-d H:i');
        } catch (\Throwable) {
            return $value;
        }
    }

    return '—';
};
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-2xl font-bold text-foreground">Admin Dashboard</h1>
        <x-ui.badge variant="outline" size="sm">Operational overview</x-ui.badge>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <x-ui.card padding="sm" class="space-y-1">
            <p class="text-xs font-bold text-muted-foreground">Online users</p>
            <p class="text-2xl font-bold text-foreground">{{ (int) ($liveSessions['online_users'] ?? 0) }}</p>
            <p class="text-xs text-muted-foreground">Active in the last 15 minutes</p>
        </x-ui.card>

        <x-ui.card padding="sm" class="space-y-1">
            <p class="text-xs font-bold text-muted-foreground">Concurrent sessions</p>
            <p class="text-2xl font-bold text-foreground">{{ (int) ($liveSessions['concurrent_sessions'] ?? 0) }}</p>
            <p class="text-xs text-muted-foreground">Includes authenticated and guest sessions</p>
        </x-ui.card>

        <x-ui.card padding="sm" class="space-y-1">
            <p class="text-xs font-bold text-muted-foreground">Guest sessions</p>
            <p class="text-2xl font-bold text-foreground">{{ (int) ($liveSessions['guest_sessions'] ?? 0) }}</p>
            <p class="text-xs text-muted-foreground">Anonymous visitors currently online</p>
        </x-ui.card>

        <x-ui.card padding="sm" class="space-y-1">
            <p class="text-xs font-bold text-muted-foreground">Total users</p>
            <p class="text-2xl font-bold text-foreground">{{ (int) ($roleBreakdown['total_users'] ?? 0) }}</p>
            <p class="text-xs text-muted-foreground">Registered accounts in the system</p>
        </x-ui.card>

        <x-ui.card padding="sm" class="space-y-2">
            <p class="text-xs font-bold text-muted-foreground">Role breakdown</p>
            <div class="space-y-1 text-sm text-foreground">
                <div class="flex items-center justify-between">
                    <span>Admins</span>
                    <span class="font-bold">{{ (int) ($roleBreakdown['admin_users'] ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Users</span>
                    <span class="font-bold">{{ (int) ($roleBreakdown['regular_users'] ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Guests</span>
                    <span class="font-bold">{{ (int) ($roleBreakdown['guest_users'] ?? 0) }}</span>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="sm" class="space-y-2">
            <p class="text-xs font-bold text-muted-foreground">Content totals</p>
            <div class="space-y-1 text-sm text-foreground">
                <div class="flex items-center justify-between">
                    <span>Photos</span>
                    <span class="font-bold">{{ (int) ($contentTotals['photos'] ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Albums</span>
                    <span class="font-bold">{{ (int) ($contentTotals['albums'] ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Posts</span>
                    <span class="font-bold">{{ (int) ($contentTotals['posts'] ?? 0) }}</span>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <x-ui.card class="space-y-4" aria-labelledby="registrations-chart-heading">
            <x-slot:header>
                <div class="flex items-center justify-between gap-2">
                    <h2 id="registrations-chart-heading" class="text-lg font-bold text-foreground">Registrations (14 days)</h2>
                    <x-ui.badge variant="muted" size="sm">{{ $registrationSeries->sum('total') }} total</x-ui.badge>
                </div>
            </x-slot:header>

            @if($registrationChartSeries->isEmpty())
                <x-ui.empty-state
                    title="No registration data available."
                    description="Daily registration metrics will appear once data is recorded."
                    compact
                    align="left"
                />
            @else
                <div class="space-y-3">
                    <svg viewBox="0 0 100 100" class="h-32 w-full rounded border border-border bg-background p-2" role="img" aria-label="Registration trend line chart">
                        <polyline points="{{ $registrationPolyline }}" fill="none" stroke="currentColor" class="text-primary" stroke-width="2"></polyline>
                        @foreach($registrationChartSeries as $point)
                            <circle cx="{{ (float) ($point['x'] ?? 0) }}" cy="{{ (float) ($point['y'] ?? 100) }}" r="1.7" class="fill-primary">
                                <title>{{ $point['label'] ?? 'Unknown' }}: {{ (int) ($point['total'] ?? 0) }}</title>
                            </circle>
                        @endforeach
                    </svg>

                    <ul class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach($registrationSeries as $point)
                            <li class="rounded border border-border bg-background p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold text-muted-foreground">{{ $point['label'] ?? 'Unknown' }}</span>
                                    <span class="text-sm font-bold text-foreground">{{ (int) ($point['total'] ?? 0) }}</span>
                                </div>
                                <div class="mt-2 h-1.5 overflow-hidden rounded border border-border bg-secondary">
                                    <div class="h-full bg-primary" style="width: {{ max(0, min(100, (int) ($point['intensity'] ?? 0))) }}%;"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-ui.card>

        <x-ui.card class="space-y-4" aria-labelledby="session-chart-heading">
            <x-slot:header>
                <div class="flex items-center justify-between gap-2">
                    <h2 id="session-chart-heading" class="text-lg font-bold text-foreground">Session Traffic (14 days)</h2>
                    <x-ui.badge variant="muted" size="sm">{{ $sessionSeries->sum('total') }} total</x-ui.badge>
                </div>
            </x-slot:header>

            @if($sessionChartSeries->isEmpty())
                <x-ui.empty-state
                    title="No session traffic data available."
                    description="Daily session activity will appear once traffic is recorded."
                    compact
                    align="left"
                />
            @else
                <div class="space-y-3">
                    <svg viewBox="0 0 100 100" class="h-32 w-full rounded border border-border bg-background p-2" role="img" aria-label="Session traffic trend line chart">
                        <polyline points="{{ $sessionPolyline }}" fill="none" stroke="currentColor" class="text-foreground" stroke-width="2"></polyline>
                        @foreach($sessionChartSeries as $point)
                            <circle cx="{{ (float) ($point['x'] ?? 0) }}" cy="{{ (float) ($point['y'] ?? 100) }}" r="1.7" class="fill-foreground">
                                <title>{{ $point['label'] ?? 'Unknown' }}: {{ (int) ($point['total'] ?? 0) }}</title>
                            </circle>
                        @endforeach
                    </svg>

                    <ul class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach($sessionSeries as $point)
                            <li class="rounded border border-border bg-background p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold text-muted-foreground">{{ $point['label'] ?? 'Unknown' }}</span>
                                    <span class="text-sm font-bold text-foreground">{{ (int) ($point['total'] ?? 0) }}</span>
                                </div>
                                <div class="mt-2 h-1.5 overflow-hidden rounded border border-border bg-secondary">
                                    <div class="h-full bg-foreground" style="width: {{ max(0, min(100, (int) ($point['intensity'] ?? 0))) }}%;"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-ui.card>
    </div>

    <x-ui.card aria-labelledby="accounts-heading">
        <x-slot:header>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 id="accounts-heading" class="text-lg font-bold text-foreground">Accounts</h2>
                <x-ui.badge variant="outline" size="sm">{{ $accounts->count() }} recent</x-ui.badge>
            </div>
        </x-slot:header>

        @if($accounts->isEmpty())
            <x-ui.empty-state
                title="No account activity available."
                description="Recently created accounts will appear here."
                compact
                align="left"
            />
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[36rem] text-left text-sm">
                    <thead>
                        <tr class="border-b border-border text-xs uppercase tracking-wide text-muted-foreground">
                            <th class="px-3 py-2 font-bold">Name</th>
                            <th class="px-3 py-2 font-bold">Email</th>
                            <th class="px-3 py-2 font-bold">Role</th>
                            <th class="px-3 py-2 font-bold">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr class="border-b border-border/80 last:border-b-0">
                                <td class="px-3 py-2 text-foreground">{{ $formatUserName($account) }}</td>
                                <td class="px-3 py-2 text-foreground">{{ $account->email }}</td>
                                <td class="px-3 py-2">
                                    <x-ui.badge variant="muted" size="sm">{{ $account->role }}</x-ui.badge>
                                </td>
                                <td class="px-3 py-2 text-muted-foreground">{{ $formatDate($account->created_at) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>

    @can('view-admin-dashboard')
        <section class="space-y-4" aria-labelledby="moderation-queues-heading">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 id="moderation-queues-heading" class="text-lg font-bold text-foreground">Moderation Queues</h2>
                <x-ui.badge variant="outline" size="sm">
                    {{ $moderationPosts->count() + $moderationPhotos->count() + $moderationAlbums->count() + $moderationMilestones->count() + $moderationGuestbook->count() }} items
                </x-ui.badge>
            </div>

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <x-ui.card padding="sm" class="space-y-3" aria-labelledby="moderation-posts-heading">
                    <div class="flex items-center justify-between gap-2">
                        <h3 id="moderation-posts-heading" class="text-sm font-bold text-foreground">Posts</h3>
                        <x-ui.badge variant="muted" size="sm">{{ $moderationPosts->count() }}</x-ui.badge>
                    </div>

                    @if($moderationPosts->isEmpty())
                        <p class="text-sm text-muted-foreground">No posts awaiting review.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($moderationPosts as $post)
                                @php
                                $postTitle = trim((string) ($post->title ?? ''));
                                if ($postTitle === '') {
                                    $postTitle = 'Untitled post #'.$post->id;
                                }
                                @endphp

                                <li class="rounded border border-border bg-background p-3">
                                    <p class="truncate text-sm font-bold text-foreground">{{ $postTitle }}</p>
                                    <p class="mt-1 text-xs text-muted-foreground">By {{ $formatUserName($post->user) }} · {{ $formatDate($post->created_at) }}</p>

                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="mt-3 flex justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="destructive" size="sm">Delete post</x-ui.button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>

                <x-ui.card padding="sm" class="space-y-3" aria-labelledby="moderation-photos-heading">
                    <div class="flex items-center justify-between gap-2">
                        <h3 id="moderation-photos-heading" class="text-sm font-bold text-foreground">Photos</h3>
                        <x-ui.badge variant="muted" size="sm">{{ $moderationPhotos->count() }}</x-ui.badge>
                    </div>

                    @if($moderationPhotos->isEmpty())
                        <p class="text-sm text-muted-foreground">No photos awaiting review.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($moderationPhotos as $photo)
                                @php
                                $photoTitle = trim((string) ($photo->title ?? ''));
                                if ($photoTitle === '') {
                                    $photoTitle = 'Photo #'.$photo->id;
                                }
                                @endphp

                                <li class="rounded border border-border bg-background p-3">
                                    <p class="truncate text-sm font-bold text-foreground">{{ $photoTitle }}</p>
                                    <p class="mt-1 text-xs text-muted-foreground">By {{ $formatUserName($photo->user) }} · {{ $formatDate($photo->created_at) }}</p>

                                    <form action="{{ route('admin.photos.destroy', $photo) }}" method="POST" class="mt-3 flex justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="destructive" size="sm">Delete photo</x-ui.button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>

                <x-ui.card padding="sm" class="space-y-3" aria-labelledby="moderation-albums-heading">
                    <div class="flex items-center justify-between gap-2">
                        <h3 id="moderation-albums-heading" class="text-sm font-bold text-foreground">Albums</h3>
                        <x-ui.badge variant="muted" size="sm">{{ $moderationAlbums->count() }}</x-ui.badge>
                    </div>

                    @if($moderationAlbums->isEmpty())
                        <p class="text-sm text-muted-foreground">No albums awaiting review.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($moderationAlbums as $album)
                                @php
                                $albumTitle = trim((string) ($album->title ?? ''));
                                if ($albumTitle === '') {
                                    $albumTitle = 'Untitled album #'.$album->id;
                                }
                                @endphp

                                <li class="rounded border border-border bg-background p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-bold text-foreground">{{ $albumTitle }}</p>
                                        <x-ui.badge variant="{{ $album->is_private ? 'secondary' : 'outline' }}" size="sm">
                                            {{ $album->is_private ? 'Private' : 'Public' }}
                                        </x-ui.badge>
                                    </div>
                                    <p class="mt-1 text-xs text-muted-foreground">By {{ $formatUserName($album->user) }} · {{ $formatDate($album->created_at) }}</p>

                                    <form action="{{ route('admin.albums.destroy', $album) }}" method="POST" class="mt-3 flex justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="destructive" size="sm">Delete album</x-ui.button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>

                <x-ui.card padding="sm" class="space-y-3" aria-labelledby="moderation-milestones-heading">
                    <div class="flex items-center justify-between gap-2">
                        <h3 id="moderation-milestones-heading" class="text-sm font-bold text-foreground">Milestones</h3>
                        <x-ui.badge variant="muted" size="sm">{{ $moderationMilestones->count() }}</x-ui.badge>
                    </div>

                    @if($moderationMilestones->isEmpty())
                        <p class="text-sm text-muted-foreground">No milestones awaiting review.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($moderationMilestones as $milestone)
                                @php
                                $milestoneLabel = trim((string) ($milestone->label ?? ''));
                                if ($milestoneLabel === '') {
                                    $milestoneLabel = 'Milestone #'.$milestone->id;
                                }
                                @endphp

                                <li class="rounded border border-border bg-background p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-bold text-foreground">{{ $milestoneLabel }}</p>
                                        <x-ui.badge variant="{{ $milestone->is_public ? 'outline' : 'secondary' }}" size="sm">
                                            {{ $milestone->is_public ? 'Public' : 'Private' }}
                                        </x-ui.badge>
                                    </div>
                                    <p class="mt-1 text-xs text-muted-foreground">By {{ $formatUserName($milestone->user) }} · {{ $formatDate($milestone->created_at) }}</p>

                                    <form action="{{ route('admin.milestones.destroy', $milestone) }}" method="POST" class="mt-3 flex justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="destructive" size="sm">Delete milestone</x-ui.button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>

                <x-ui.card padding="sm" class="space-y-3 xl:col-span-2" aria-labelledby="moderation-guestbook-heading">
                    <div class="flex items-center justify-between gap-2">
                        <h3 id="moderation-guestbook-heading" class="text-sm font-bold text-foreground">Guestbook</h3>
                        <x-ui.badge variant="muted" size="sm">{{ $moderationGuestbook->count() }}</x-ui.badge>
                    </div>

                    @if($moderationGuestbook->isEmpty())
                        <p class="text-sm text-muted-foreground">No guestbook entries awaiting review.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($moderationGuestbook as $entry)
                                @php
                                $entryTitle = trim((string) ($entry->post?->title ?? ''));
                                if ($entryTitle === '') {
                                    $entryTitle = 'Guestbook entry #'.$entry->id;
                                }
                                @endphp

                                <li class="rounded border border-border bg-background p-3">
                                    <p class="truncate text-sm font-bold text-foreground">{{ $entryTitle }}</p>
                                    <p class="mt-1 text-xs text-muted-foreground">By {{ $formatUserName($entry->post?->user) }} · {{ $formatDate($entry->created_at) }}</p>

                                    <form action="{{ route('admin.guestbook.destroy', $entry) }}" method="POST" class="mt-3 flex justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="destructive" size="sm">Delete entry</x-ui.button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>
            </div>
        </section>
    @endcan
</div>
@endsection
