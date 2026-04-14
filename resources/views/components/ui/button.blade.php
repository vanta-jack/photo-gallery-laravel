@props([
    'as' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'block' => false,
])

@php
$tag = in_array($as, ['button', 'a'], true) ? $as : 'button';

$variantClasses = match ($variant) {
    'secondary' => 'bg-secondary text-secondary-foreground border-border hover:opacity-90',
    'ghost' => 'bg-transparent text-foreground border-transparent hover:bg-secondary',
    'destructive' => 'bg-card text-destructive border-destructive hover:bg-destructive/10',
    'link' => 'bg-transparent text-foreground border-transparent underline-offset-4 hover:underline',
    default => 'bg-primary text-primary-foreground border-primary hover:opacity-90',
};

$sizeClasses = $variant === 'link'
    ? match ($size) {
        'sm' => 'text-xs',
        'lg' => 'text-base',
        default => 'text-sm',
    }
    : match ($size) {
        'sm' => 'px-3 py-2 text-xs',
        'lg' => 'px-5 py-3 text-sm',
        default => 'px-4 py-2 text-sm',
    };

$baseAttributes = $attributes->class([
    'cursor-pointer select-none inline-flex items-center justify-center gap-2 rounded border font-bold transition-opacity duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-60',
    $variantClasses,
    $sizeClasses,
    'w-full' => $block,
]);

// Add iOS/webkit-specific styles for proper touch interaction
$iosStyles = 'touch-action: manipulation; -webkit-appearance: button; -webkit-user-select: none; -webkit-touch-callout: none;';
$baseAttributes = $baseAttributes->merge(['style' => $iosStyles]);

if ($tag === 'button') {
    $baseAttributes = $baseAttributes->merge(['type' => $type]);
}
@endphp

<{{ $tag }} {{ $baseAttributes }}>
    {{ $slot }}
</{{ $tag }}>
