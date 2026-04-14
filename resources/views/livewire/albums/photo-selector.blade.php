<div class="space-y-2" wire:key="photo-selector-{{ $componentId }}">
    <label for="{{ $componentId }}-group" class="block text-sm font-bold text-foreground">
        {{ $label }}
    </label>

    @if($help)
        <p class="text-xs text-muted-foreground">{{ $help }}</p>
    @endif

    @if(empty($photos))
        <div class="rounded border border-border bg-secondary/30 p-4 text-center">
            <p class="text-sm text-muted-foreground">No photos available yet.</p>
        </div>
    @else
        <fieldset id="{{ $componentId }}-group" class="space-y-3">
            <div class="flex flex-wrap gap-2">
                @if($selected !== null)
                    <button
                        type="button"
                        wire:click="clearSelection"
                        class="inline-flex items-center gap-1 rounded border border-border bg-secondary px-2 py-1 text-xs font-bold text-secondary-foreground transition-opacity hover:opacity-80"
                    >
                        <x-icon name="x" class="w-3 h-3" />
                        Clear selection
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                @foreach($photos as $photo)
                    @php
                    $photoId = $photo->id ?? null;
                    $photoTitle = trim((string) ($photo->title ?? '')) !== ''
                        ? $photo->title
                        : 'Photo #' . $photoId;
                    $path = trim((string) ($photo->path ?? ''));
                    $imageUrl = $path === ''
                        ? null
                        : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
                            ? $path
                            : \Illuminate\Support\Facades\Storage::url($path));
                    $isSelected = $photoId === $selected;
                    @endphp

                    <label class="group relative cursor-pointer" role="radio">
                        <input
                            type="radio"
                            name="{{ $name }}"
                            value="{{ $photoId }}"
                            wire:model.live="selected"
                            @checked($isSelected)
                            class="absolute inset-0 opacity-0"
                        >

                        <div
                            class="
                                relative overflow-hidden rounded border-2
                                transition-all duration-150
                                {{ $isSelected
                                    ? 'border-primary bg-primary/10 shadow-md'
                                    : 'border-border hover:border-foreground/30'
                                }}
                            "
                            style="width: 100px; height: 100px;"
                        >
                            @if($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $photoTitle }}"
                                    title="{{ $photoTitle }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-secondary">
                                    <x-icon name="image" class="w-6 h-6 text-muted-foreground" />
                                </div>
                            @endif

                            @if($isSelected)
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                    <x-icon name="check" class="w-5 h-5 text-primary" />
                                </div>
                            @endif
                        </div>

                        <span class="sr-only">{{ $photoTitle }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endif
</div>
