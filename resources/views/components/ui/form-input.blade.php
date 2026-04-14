@props([
    'name' => null,
    'label' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'help' => null,
    'errorKey' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => null,
])

@php
$wireModelAttributes = $attributes->whereStartsWith('wire:model')->getAttributes();
$wireModel = count($wireModelAttributes) > 0 ? array_values($wireModelAttributes)[0] : null;
$fieldKey = $errorKey ?? $name ?? $wireModel;
$hasWireModel = $wireModel !== null;

$idSource = $id ?? $name ?? $wireModel ?? ($label ? strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $label)) : null);
$inputId = $idSource ? preg_replace('/[^A-Za-z0-9\-_:.]/', '_', (string) $idSource) : null;

$hasError = $fieldKey ? $errors->has($fieldKey) : false;
$resolvedValue = $value;

if (! $hasWireModel && $name && ! $attributes->has('value') && ! in_array($type, ['password', 'file'], true)) {
    $resolvedValue = old($name, $value);
}

$shouldRenderValue = ! in_array($type, ['password', 'file'], true)
    && ! is_null($resolvedValue)
    && (! $hasWireModel || $attributes->has('value') || ! is_null($value));

$describedBy = null;
if ($inputId && $hasError) {
    $describedBy = $inputId.'-error';
} elseif ($inputId && $help) {
    $describedBy = $inputId.'-help';
}
@endphp

<div class="space-y-2">
    @if($label)
        <label @if($inputId) for="{{ $inputId }}" @endif class="block text-sm font-bold text-foreground">
            {{ $label }}
            @if($required)
                <span class="text-destructive" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <input
        {{ $attributes
            ->class([
                'block w-full rounded border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-60',
                $hasError ? 'border-destructive focus-visible:ring-destructive' : 'border-input focus-visible:ring-ring',
            ])
            ->merge([
                'id' => $inputId,
                'name' => $name,
                'type' => $type,
                'placeholder' => $placeholder,
                'autocomplete' => $autocomplete,
                'aria-invalid' => $hasError ? 'true' : null,
                'aria-describedby' => $describedBy,
            ]) }}
        @required($required)
        @disabled($disabled)
        @readonly($readonly)
        @if($shouldRenderValue)
            value="{{ $resolvedValue }}"
        @endif
    />

    @if($hasError && $fieldKey)
        <p @if($inputId) id="{{ $inputId }}-error" @endif class="text-sm text-destructive">
            {{ $errors->first($fieldKey) }}
        </p>
    @elseif($help)
        <p @if($inputId) id="{{ $inputId }}-help" @endif class="text-sm text-muted-foreground">
            {{ $help }}
        </p>
    @endif
</div>
