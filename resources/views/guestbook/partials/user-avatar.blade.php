@php
/** @var \App\Models\User|null $user */
$user = $user ?? null;
$name = trim((string) ($name ?? ''));
$photoPath = trim((string) ($photoPath ?? ($user?->profilePhoto?->path ?? '')));
$meta = trim((string) ($meta ?? ''));
$showName = (bool) ($showName ?? true);
$requestedSize = $size ?? 'md';
$size = in_array($requestedSize, ['sm', 'md', 'lg'], true) ? $requestedSize : 'md';

$firstName = trim((string) ($user?->first_name ?? ''));
$lastName = trim((string) ($user?->last_name ?? ''));
$resolvedName = $name !== '' ? $name : trim(sprintf('%s %s', $firstName, $lastName));

if ($resolvedName === '') {
    $resolvedName = 'Anonymous';
}

$initials = collect(preg_split('/\s+/', $resolvedName) ?: [])
    ->filter()
    ->take(2)
    ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
    ->implode('');

if ($initials === '') {
    $initials = 'AN';
}

$avatarSizeClasses = match ($size) {
    'sm' => 'h-9 w-9 text-xs',
    'lg' => 'h-14 w-14 text-base',
    default => 'h-11 w-11 text-sm',
};

$imageUrl = null;
if ($photoPath !== '') {
    $imageUrl = str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://') || str_starts_with($photoPath, '/')
        ? $photoPath
        : \Illuminate\Support\Facades\Storage::url($photoPath);
}
@endphp

<div class="flex min-w-0 items-center gap-3">
    <div class="{{ $avatarSizeClasses }} shrink-0 overflow-hidden rounded-full border border-border bg-secondary text-secondary-foreground">
        @if($imageUrl)
            <img
                src="{{ $imageUrl }}"
                alt="{{ $resolvedName }} profile image"
                class="h-full w-full rounded-full object-cover"
                loading="lazy"
            >
        @else
            <span class="flex h-full w-full items-center justify-center font-bold" aria-hidden="true">{{ $initials }}</span>
            <span class="sr-only">{{ $resolvedName }}</span>
        @endif
    </div>

    @if($showName)
        <div class="min-w-0">
            <p class="truncate text-sm font-bold text-foreground">{{ $resolvedName }}</p>
            @if($meta !== '')
                <p class="truncate text-xs text-muted-foreground">{{ $meta }}</p>
            @endif
        </div>
    @endif
</div>
