@props([
    'variant' => 'info',
    'title' => null,
    'description' => null,
    'role' => null,
    'dismissible' => false,
    'dismissLabel' => 'Dismiss',
])

@php
$variantClasses = match ($variant) {
    'muted' => 'bg-secondary text-secondary-foreground border-border',
    'warning' => 'bg-secondary text-foreground border-border',
    'destructive' => 'bg-card text-destructive border-destructive',
    default => 'bg-card text-card-foreground border-border',
};

$resolvedRole = $role ?? (in_array($variant, ['warning', 'destructive'], true) ? 'alert' : 'status');
@endphp

<div
    {{ $attributes
        ->class(['rounded border p-4', $variantClasses])
        ->merge(['role' => $resolvedRole]) }}
>
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-start gap-3">
            @isset($icon)
                <span class="mt-0.5 shrink-0 text-current">
                    {{ $icon }}
                </span>
            @endisset

            <div class="space-y-1">
                @if($title)
                    <p class="text-sm font-bold">{{ $title }}</p>
                @endif

                @if($description)
                    <p class="text-sm">{{ $description }}</p>
                @endif

                @if(trim((string) $slot) !== '')
                    <div class="text-sm">
                        {{ $slot }}
                    </div>
                @endif
            </div>
        </div>

        @if($dismissible)
            <button
                type="button"
                class="inline-flex items-center rounded border border-current px-2 py-1 text-xs font-bold opacity-80 transition-opacity duration-150 hover:opacity-100"
                data-alert-close
                aria-label="{{ $dismissLabel }}"
            >
                {{ $dismissLabel }}
            </button>
        @endif
    </div>
</div>
