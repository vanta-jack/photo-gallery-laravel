@props([
    'html' => null,
    'size' => 'sm',
])

@if(filled($html))
    <div
        {{ $attributes->class([
            'prose max-w-none text-foreground prose-headings:text-foreground prose-strong:text-foreground prose-a:text-foreground prose-ul:list-disc prose-ol:list-decimal prose-li:marker:text-foreground prose-blockquote:border-l-border prose-blockquote:text-muted-foreground',
            'prose-sm' => $size === 'sm',
            'prose-base' => $size === 'base',
        ]) }}
    >
        {!! $html !!}
    </div>
@endif
