<section
    class="space-y-2"
    wire:key="markdown-preview-{{ $previewId }}"
    data-markdown-preview="{{ $previewId }}"
    aria-label="Markdown preview"
>
    <h3 class="text-sm font-bold text-foreground">Preview</h3>
    <div class="min-h-[10rem] rounded border border-border bg-card p-3">
        @if(filled($previewHtml))
            <x-ui.markdown-content :html="$previewHtml" />
        @else
            <p class="text-sm text-muted-foreground">Preview will appear as markdown updates.</p>
        @endif
    </div>
</section>
