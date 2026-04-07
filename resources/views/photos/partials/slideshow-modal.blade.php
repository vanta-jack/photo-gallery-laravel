@if($slideshowPhotos->isNotEmpty())
    <div
        class="fixed inset-0 z-50 hidden bg-background/95 text-foreground"
        data-slideshow-root
        aria-hidden="true"
    >
        <div class="h-full w-full flex flex-col">
            <div class="flex items-center justify-between border-b border-border px-4 py-3" data-slideshow-controls>
                <div class="text-xs text-muted-foreground">
                    <span data-slideshow-counter>1 / {{ $slideshowPhotos->count() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="bg-secondary text-secondary-foreground font-bold text-xs px-3 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150" data-slideshow-toggle-autoplay>
                        Play
                    </button>
                    <button type="button" class="bg-secondary text-secondary-foreground font-bold text-xs px-3 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150" data-slideshow-close>
                        Close
                    </button>
                </div>
            </div>

            <div class="flex-1 flex items-center justify-between px-2 sm:px-6 py-4">
                <button type="button" class="bg-secondary text-secondary-foreground border border-border rounded p-2 hover:opacity-90 transition-opacity duration-150" data-slideshow-prev aria-label="Previous photo">
                    <x-icon name="chevron-left" class="w-6 h-6" />
                </button>

                <button type="button" class="relative max-w-6xl w-full h-full mx-2 sm:mx-6 border border-border rounded overflow-hidden bg-card" data-slideshow-stage aria-label="Toggle controls">
                    <img src="" alt="" class="w-full h-full object-contain transition-opacity duration-200" data-slideshow-image>
                </button>

                <button type="button" class="bg-secondary text-secondary-foreground border border-border rounded p-2 hover:opacity-90 transition-opacity duration-150" data-slideshow-next aria-label="Next photo">
                    <x-icon name="chevron-right" class="w-6 h-6" />
                </button>
            </div>

            <div class="border-t border-border px-4 py-3 space-y-2" data-slideshow-controls>
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-bold text-foreground truncate" data-slideshow-title></h2>
                    <a href="#" class="text-xs font-bold text-primary hover:underline" data-slideshow-detail-link>View photo</a>
                </div>
                <p class="text-xs text-muted-foreground" data-slideshow-description></p>
                <p class="text-[11px] text-muted-foreground" data-slideshow-date></p>
            </div>
        </div>

        <script type="application/json" data-slideshow-photos>@json($slideshowPhotos)</script>
    </div>
@endif
