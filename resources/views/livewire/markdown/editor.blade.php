<div class="grid grid-cols-1 gap-4 lg:grid-cols-2" wire:key="markdown-editor-{{ $id }}" data-markdown-editor>
    <div class="space-y-2">
        <div class="space-y-2 rounded border border-border bg-card p-3">
            <div class="flex flex-wrap gap-1" aria-label="{{ $label }} markdown toolbar" data-markdown-toolbar>
                <button type="button" data-md-action="bold" class="inline-flex items-center justify-center rounded p-1.5 text-foreground transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-card focus:ring-accent pointer-events-auto cursor-pointer touch-manipulation" title="Bold" aria-label="Bold (Ctrl/Cmd + B)">
                    <x-icon name="bold" class="w-4 h-4" />
                </button>
                <button type="button" data-md-action="italic" class="inline-flex items-center justify-center rounded p-1.5 text-foreground transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-card focus:ring-accent pointer-events-auto cursor-pointer touch-manipulation" title="Italic" aria-label="Italic (Ctrl/Cmd + I)">
                    <x-icon name="italic" class="w-4 h-4" />
                </button>
                <button type="button" data-md-action="heading" class="inline-flex items-center justify-center rounded p-1.5 text-foreground transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-card focus:ring-accent pointer-events-auto cursor-pointer touch-manipulation" title="Heading" aria-label="Heading (Ctrl/Cmd + H)">
                    <x-icon name="heading" class="w-4 h-4" />
                </button>
                <button type="button" data-md-action="quote" class="inline-flex items-center justify-center rounded p-1.5 text-foreground transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-card focus:ring-accent pointer-events-auto cursor-pointer touch-manipulation" title="Blockquote" aria-label="Blockquote">
                    <x-icon name="quote" class="w-4 h-4" />
                </button>
                <div class="w-px bg-border"></div>
                <button type="button" data-md-action="unordered-list" class="inline-flex items-center justify-center rounded p-1.5 text-foreground transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-card focus:ring-accent pointer-events-auto cursor-pointer touch-manipulation" title="Unordered List" aria-label="Unordered List">
                    <x-icon name="list" class="w-4 h-4" />
                </button>
                <button type="button" data-md-action="ordered-list" class="inline-flex items-center justify-center rounded p-1.5 text-foreground transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-card focus:ring-accent pointer-events-auto cursor-pointer touch-manipulation" title="Ordered List" aria-label="Ordered List">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="10" y1="6" x2="21" y2="6"></line>
                        <line x1="10" y1="12" x2="21" y2="12"></line>
                        <line x1="10" y1="18" x2="21" y2="18"></line>
                        <path d="M4 6h1v4"></path>
                        <path d="M4 10h2"></path>
                        <path d="M6 18H4c0-1 .773-2 1.5-2.5S7 15.5 7 14c0-1-.227-2-1-2.5S3.773 11 4 10"></path>
                    </svg>
                </button>
                <div class="w-px bg-border"></div>
                <button type="button" data-md-action="link" class="inline-flex items-center justify-center rounded p-1.5 text-foreground transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-card focus:ring-accent pointer-events-auto cursor-pointer touch-manipulation" title="Link" aria-label="Link">
                    <x-icon name="link-2" class="w-4 h-4" />
                </button>
                <button type="button" data-md-action="code" class="inline-flex items-center justify-center rounded p-1.5 text-foreground transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-card focus:ring-accent pointer-events-auto cursor-pointer touch-manipulation" title="Code" aria-label="Inline Code">
                    <x-icon name="code" class="w-4 h-4" />
                </button>
            </div>

            <x-ui.form-textarea
                :name="$name"
                :id="$id"
                :label="$label"
                :rows="$rows"
                :placeholder="$placeholder"
                :help="$help"
                :required="$required"
                :error-key="$name"
                data-markdown-input
                wire:model.live.debounce.250ms="content"
            />
        </div>
    </div>

    <section class="space-y-2" aria-label="{{ $label }} preview">
        <h3 class="text-sm font-bold text-foreground">Preview</h3>
        <div class="min-h-[10rem] rounded border border-border bg-card p-3">
            @if(filled($previewHtml))
                <x-ui.markdown-content :html="$previewHtml" />
            @else
                <p class="text-sm text-muted-foreground">Preview will appear as you type markdown.</p>
            @endif
        </div>
    </section>
</div>
