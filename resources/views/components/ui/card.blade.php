@props([
    'as' => 'div',
    'padding' => 'md',
])

@php
$paddingClasses = match ($padding) {
    'none' => '',
    'sm' => 'p-4',
    'lg' => 'p-8',
    default => 'p-6',
};
@endphp

<{{ $as }} {{ $attributes->class(['bg-card text-card-foreground border border-border rounded', $paddingClasses]) }}>
    @isset($header)
        <div class="mb-4 border-b border-border pb-4">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-4 border-t border-border pt-4">
            {{ $footer }}
        </div>
    @endisset
</{{ $as }}>
