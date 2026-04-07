@php
    $firstName = trim((string) ($user?->first_name ?? ''));
    $lastName = trim((string) ($user?->last_name ?? ''));
    $displayName = trim($firstName.' '.$lastName);
    $displayName = $displayName !== '' ? $displayName : 'Guest';
    $initials = mb_strtoupper(mb_substr($firstName ?: $displayName, 0, 1).mb_substr($lastName, 0, 1));
    $initials = trim($initials) !== '' ? $initials : 'G';
@endphp

<div class="h-10 w-10 shrink-0 overflow-hidden rounded border border-border bg-muted/40">
    @if($user?->profilePhoto?->path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profilePhoto->path) }}" alt="{{ $displayName }} avatar" class="h-full w-full object-cover">
    @else
        <div class="h-full w-full flex items-center justify-center text-xs font-bold text-muted-foreground">{{ $initials }}</div>
    @endif
</div>
