@props([
    'type' => 'post',
    'size' => 'md',
])

@php
$typeConfig = match ($type) {
    'milestone' => [
        'label' => 'Milestone',
        'icon' => 'star',
        'lightBg' => 'bg-amber-300',
        'lightText' => 'text-black',
        'darkBg' => 'dark:bg-amber-600',
        'darkText' => 'dark:text-white',
    ],
    'post' => [
        'label' => 'Post',
        'icon' => 'pen',
        'lightBg' => 'bg-blue-600',
        'lightText' => 'text-white',
        'darkBg' => 'dark:bg-blue-400',
        'darkText' => 'dark:text-black',
    ],
    'album' => [
        'label' => 'Album',
        'icon' => 'images',
        'lightBg' => 'bg-red-600',
        'lightText' => 'text-white',
        'darkBg' => 'dark:bg-red-400',
        'darkText' => 'dark:text-black',
    ],
    'guestbook' => [
        'label' => 'Guestbook',
        'icon' => 'book-open',
        'lightBg' => 'bg-emerald-600',
        'lightText' => 'text-white',
        'darkBg' => 'dark:bg-emerald-400',
        'darkText' => 'dark:text-black',
    ],
    'photo' => [
        'label' => 'Photo',
        'icon' => 'image',
        'lightBg' => 'bg-gray-200',
        'lightText' => 'text-black',
        'darkBg' => 'dark:bg-gray-700',
        'darkText' => 'dark:text-white',
    ],
    default => [
        'label' => ucfirst($type),
        'icon' => 'image',
        'lightBg' => 'bg-gray-200',
        'lightText' => 'text-black',
        'darkBg' => 'dark:bg-gray-700',
        'darkText' => 'dark:text-white',
    ],
};

$sizeClasses = match ($size) {
    'sm' => 'px-2 py-0.5 text-xs gap-1',
    'lg' => 'px-3 py-1.5 text-base gap-2',
    default => 'px-2.5 py-1 text-sm gap-1.5',
};
@endphp

<span
    {{ $attributes->class([
        'inline-flex items-center rounded border font-bold whitespace-nowrap',
        'transition-colors duration-200',
        $typeConfig['lightBg'],
        $typeConfig['lightText'],
        $typeConfig['darkBg'],
        $typeConfig['darkText'],
        'border-current',
        $sizeClasses,
    ]) }}
>
    <x-icon 
        :name="$typeConfig['icon']"
        :class="match($size) {
            'sm' => 'w-3 h-3',
            'lg' => 'w-5 h-5',
            default => 'w-4 h-4',
        }"
    />
    {{ $typeConfig['label'] }}
</span>
