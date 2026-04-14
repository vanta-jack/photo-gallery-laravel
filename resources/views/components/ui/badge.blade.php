@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php
$variantClasses = match ($variant) {
    'primary' => 'bg-primary text-primary-foreground border-primary',
    'secondary' => 'bg-secondary text-secondary-foreground border-border',
    'muted' => 'bg-muted text-muted-foreground border-border',
    'destructive' => 'bg-card text-destructive border-destructive',
    'outline' => 'bg-transparent text-foreground border-border',
    default => 'bg-secondary text-secondary-foreground border-border',
};

$sizeClasses = match ($size) {
    'sm' => 'px-2 py-0.5 text-xs',
    default => 'px-2.5 py-1 text-sm',
};
@endphp

<span
    {{ $attributes->class([
        'inline-flex items-center gap-1 rounded border font-bold',
        $variantClasses,
        $sizeClasses,
    ]) }}
>
    {{ $slot }}
</span>
