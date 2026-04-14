@props([
    'id' => null,
    'title' => null,
    'description' => null,
    'open' => false,
    'maxWidth' => 'lg',
    'padding' => 'md',
    'showClose' => true,
    'closeLabel' => 'Close',
])

@php
$widthClasses = match ($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    'full' => 'max-w-full',
    default => 'max-w-lg',
};

$paddingClasses = match ($padding) {
    'sm' => 'p-4',
    'lg' => 'p-8',
    default => 'p-6',
};

$defaultId = $title
    ? strtolower('modal-'.preg_replace('/[^a-zA-Z0-9]+/', '-', (string) $title))
    : 'modal-dialog';

$modalId = $id ?? trim($defaultId, '-');
$titleId = $title ? $modalId.'-title' : null;
$descriptionId = $description ? $modalId.'-description' : null;
$hasHeader = $title || $description || isset($header) || $showClose;
@endphp

<div
    {{ $attributes
        ->class([
            'fixed inset-0 z-40 flex items-center justify-center bg-background/85 px-4',
            'hidden' => ! $open,
        ])
        ->merge([
            'data-modal' => '',
            'id' => $modalId,
            'role' => 'dialog',
            'aria-modal' => 'true',
            'aria-hidden' => $open ? 'false' : 'true',
            'aria-labelledby' => $titleId,
            'aria-describedby' => $descriptionId,
            'hidden' => $open ? null : 'hidden',
        ]) }}
>
    <div class="w-full {{ $widthClasses }} rounded border border-border bg-card text-card-foreground">
        <div class="{{ $paddingClasses }}">
            @if($hasHeader)
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        @isset($header)
                            {{ $header }}
                        @else
                            @if($title)
                                <h2 id="{{ $titleId }}" class="text-lg font-bold text-foreground">{{ $title }}</h2>
                            @endif

                            @if($description)
                                <p id="{{ $descriptionId }}" class="text-sm text-muted-foreground">{{ $description }}</p>
                            @endif
                        @endisset
                    </div>

                    @if($showClose)
                        <button
                            type="button"
                            class="inline-flex items-center rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                            data-modal-close
                            data-modal-id="{{ $modalId }}"
                            aria-label="{{ $closeLabel }}"
                        >
                            {{ $closeLabel }}
                        </button>
                    @endif
                </div>
            @endif

            <div @class(['mt-4' => $hasHeader])>
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="mt-6 flex items-center justify-end gap-2 border-t border-border pt-4">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
