@props([
    'title' => 'Nothing here yet.',
    'description' => null,
    'align' => 'center',
    'compact' => false,
])

@php
$alignmentClasses = $align === 'left' ? 'items-start text-left' : 'items-center text-center';
@endphp

<section
    {{ $attributes->class([
        'rounded border border-dashed border-border bg-card text-card-foreground',
        'p-5' => $compact,
        'p-8' => ! $compact,
    ]) }}
>
    <div class="mx-auto flex max-w-lg flex-col gap-3 {{ $alignmentClasses }}">
        @isset($icon)
            <div class="@if($align !== 'left') mx-auto @endif text-muted-foreground">
                {{ $icon }}
            </div>
        @endisset

        <h2 class="text-lg font-bold text-foreground">{{ $title }}</h2>

        @if($description)
            <p class="text-sm text-muted-foreground">{{ $description }}</p>
        @endif

        @if(trim((string) $slot) !== '')
            <div class="text-sm text-muted-foreground">
                {{ $slot }}
            </div>
        @endif

        @isset($actions)
            <div class="mt-2 flex flex-wrap gap-2 @if($align !== 'left') justify-center @endif">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
