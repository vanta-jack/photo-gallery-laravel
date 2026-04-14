@props([
    'name' => null,
    'label' => null,
    'id' => null,
    'value' => null,
    'rows' => 4,
    'placeholder' => null,
    'help' => null,
    'errorKey' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
])

@php
$wireModelAttributes = $attributes->whereStartsWith('wire:model')->getAttributes();
$wireModel = count($wireModelAttributes) > 0 ? array_values($wireModelAttributes)[0] : null;
$fieldKey = $errorKey ?? $name ?? $wireModel;
$hasWireModel = $wireModel !== null;

$idSource = $id ?? $name ?? $wireModel ?? ($label ? strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $label)) : null);
$textareaId = $idSource ? preg_replace('/[^A-Za-z0-9\-_:.]/', '_', (string) $idSource) : null;

$hasError = $fieldKey ? $errors->has($fieldKey) : false;
$slotValue = trim((string) $slot);
$resolvedValue = $slotValue !== '' ? $slotValue : $value;

if (! $hasWireModel && $name && $slotValue === '') {
    $resolvedValue = old($name, $value);
}

$describedBy = null;
if ($textareaId && $hasError) {
    $describedBy = $textareaId.'-error';
} elseif ($textareaId && $help) {
    $describedBy = $textareaId.'-help';
}
@endphp

<div class="space-y-2">
    @if($label)
        <label @if($textareaId) for="{{ $textareaId }}" @endif class="block text-sm font-bold text-foreground">
            {{ $label }}
            @if($required)
                <span class="text-destructive" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <textarea
        {{ $attributes
            ->class([
                'block w-full rounded border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-60',
                $hasError ? 'border-destructive focus-visible:ring-destructive' : 'border-input focus-visible:ring-ring',
            ])
            ->merge([
                'id' => $textareaId,
                'name' => $name,
                'rows' => $rows,
                'placeholder' => $placeholder,
                'aria-invalid' => $hasError ? 'true' : null,
                'aria-describedby' => $describedBy,
            ]) }}
        @required($required)
        @disabled($disabled)
        @readonly($readonly)
    >{{ $resolvedValue }}</textarea>

    @if($hasError && $fieldKey)
        <p @if($textareaId) id="{{ $textareaId }}-error" @endif class="text-sm text-destructive">
            {{ $errors->first($fieldKey) }}
        </p>
    @elseif($help)
        <p @if($textareaId) id="{{ $textareaId }}-help" @endif class="text-sm text-muted-foreground">
            {{ $help }}
        </p>
    @endif
</div>
