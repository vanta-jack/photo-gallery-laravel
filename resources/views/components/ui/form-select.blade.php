@props([
    'name' => null,
    'label' => null,
    'id' => null,
    'value' => null,
    'options' => [],
    'placeholder' => null,
    'help' => null,
    'errorKey' => null,
    'required' => false,
    'disabled' => false,
])

@php
$wireModelAttributes = $attributes->whereStartsWith('wire:model')->getAttributes();
$wireModel = count($wireModelAttributes) > 0 ? array_values($wireModelAttributes)[0] : null;
$fieldKey = $errorKey ?? $name ?? $wireModel;
$hasWireModel = $wireModel !== null;
$allowsMultiple = $attributes->has('multiple');

$idSource = $id ?? $name ?? $wireModel ?? ($label ? strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $label)) : null);
$selectId = $idSource ? preg_replace('/[^A-Za-z0-9\-_:.]/', '_', (string) $idSource) : null;

$hasError = $fieldKey ? $errors->has($fieldKey) : false;
$resolvedValue = (! $hasWireModel && $name) ? old($name, $value) : $value;

$selectedValues = collect(is_array($resolvedValue) ? $resolvedValue : [$resolvedValue])
    ->filter(static fn ($item) => ! is_null($item))
    ->map(static fn ($item) => (string) $item)
    ->values()
    ->all();

$describedBy = null;
if ($selectId && $hasError) {
    $describedBy = $selectId.'-error';
} elseif ($selectId && $help) {
    $describedBy = $selectId.'-help';
}
@endphp

<div class="space-y-2">
    @if($label)
        <label @if($selectId) for="{{ $selectId }}" @endif class="block text-sm font-bold text-foreground">
            {{ $label }}
            @if($required)
                <span class="text-destructive" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <select
        {{ $attributes
            ->class([
                'block w-full rounded border bg-background px-3 py-2 text-sm text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-60',
                $hasError ? 'border-destructive focus-visible:ring-destructive' : 'border-input focus-visible:ring-ring',
            ])
            ->merge([
                'id' => $selectId,
                'name' => $name,
                'aria-invalid' => $hasError ? 'true' : null,
                'aria-describedby' => $describedBy,
            ]) }}
        @required($required)
        @disabled($disabled)
    >
        @if($placeholder && ! $allowsMultiple)
            <option value="" @selected($selectedValues === [])>{{ $placeholder }}</option>
        @endif

        @if(trim((string) $slot) !== '')
            {{ $slot }}
        @else
            @foreach($options as $optionValue => $optionLabel)
                @php
                $currentValue = $optionValue;
                $currentLabel = $optionLabel;
                $isDisabled = false;

                if (is_array($optionLabel)) {
                    $currentValue = $optionLabel['value'] ?? $optionValue;
                    $currentLabel = $optionLabel['label'] ?? $currentValue;
                    $isDisabled = (bool) ($optionLabel['disabled'] ?? false);
                }
                @endphp

                <option
                    value="{{ $currentValue }}"
                    @selected(in_array((string) $currentValue, $selectedValues, true))
                    @disabled($isDisabled)
                >
                    {{ $currentLabel }}
                </option>
            @endforeach
        @endif
    </select>

    @if($hasError && $fieldKey)
        <p @if($selectId) id="{{ $selectId }}-error" @endif class="text-sm text-destructive">
            {{ $errors->first($fieldKey) }}
        </p>
    @elseif($help)
        <p @if($selectId) id="{{ $selectId }}-help" @endif class="text-sm text-muted-foreground">
            {{ $help }}
        </p>
    @endif
</div>
