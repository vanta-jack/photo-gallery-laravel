@props([
    'label' => 'Photos',
    'help' => null,
    'availablePhotos' => [],
    'selectedPhotoIds' => [],
    'mainPick' => null,
    'photoIdsName' => 'photo_ids',
    'mainPickName' => 'main_photo_pick',
    'legacyMainIdName' => null,
    'legacyMainId' => null,
    'allowExisting' => true,
    'allowUpload' => true,
    'uploadTitle' => 'Upload photos',
    'uploadHelp' => 'Supported formats: WebP, PNG, JPEG.',
    'required' => false,
])

@php
$componentId = 'photo-attachments-'.uniqid();
$existingPhotos = collect($availablePhotos);
$selectedIds = collect($selectedPhotoIds ?? [])
    ->map(static fn ($id): int => (int) $id)
    ->filter(static fn (int $id): bool => $id > 0)
    ->unique()
    ->values()
    ->all();
$resolvedMainPick = is_string($mainPick) && $mainPick !== '' ? $mainPick : null;
$resolvedLegacyMainId = is_numeric($legacyMainId) ? (int) $legacyMainId : 0;

if ($resolvedMainPick === null && $resolvedLegacyMainId > 0) {
    $resolvedMainPick = 'existing:'.$resolvedLegacyMainId;
}
@endphp

<section {{ $attributes->class(['space-y-3 rounded border border-border bg-card p-4']) }} data-photo-attachments-root>
    <div class="space-y-1">
        <h2 class="text-sm font-bold text-foreground">
            {{ $label }}
            @if($required)
                <span class="text-destructive" aria-hidden="true">*</span>
            @endif
        </h2>
        @if($help)
            <p class="text-xs text-muted-foreground">{{ $help }}</p>
        @endif
    </div>

    @if($allowExisting)
        <div class="space-y-2">
            <p class="text-xs font-bold uppercase tracking-wide text-muted-foreground">Select existing photos</p>

            @if($existingPhotos->isEmpty())
                <p class="text-sm text-muted-foreground">No existing photos available.</p>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($existingPhotos as $photo)
                        @php
                        $photoId = (int) ($photo->id ?? 0);
                        $path = trim((string) ($photo->path ?? ''));
                        $imageUrl = $path === ''
                            ? null
                            : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
                                ? $path
                                : \Illuminate\Support\Facades\Storage::url($path));
                        $photoTitle = trim((string) ($photo->title ?? ''));
                        if ($photoTitle === '') {
                            $photoTitle = 'Photo #'.$photoId;
                        }
                        $isSelected = in_array($photoId, $selectedIds, true);
                        $isMain = $resolvedMainPick === 'existing:'.$photoId;
                        @endphp

                        <article class="rounded border border-border p-3">
                            <label class="flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    name="{{ $photoIdsName }}[]"
                                    value="{{ $photoId }}"
                                    data-photo-existing-id="{{ $photoId }}"
                                    @checked($isSelected)
                                    class="mt-1 h-4 w-4 rounded border border-input bg-background text-primary focus-visible:ring-2 focus-visible:ring-ring"
                                >

                                <div class="min-w-0 flex-1 space-y-2">
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $photoTitle }}" class="h-24 w-full rounded border border-border object-cover" loading="lazy">
                                    @endif
                                    <p class="truncate text-sm font-bold text-foreground">{{ $photoTitle }}</p>
                                </div>
                            </label>

                            <label class="mt-2 inline-flex items-center gap-2 text-xs text-muted-foreground">
                                <input
                                    type="radio"
                                    name="{{ $componentId }}-main-existing"
                                    value="{{ $photoId }}"
                                    data-photo-main-existing="{{ $photoId }}"
                                    @checked($isMain)
                                    @disabled(! $isSelected)
                                    class="h-4 w-4 border-input text-primary focus-visible:ring-ring"
                                >
                                Use as main image
                            </label>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if($allowUpload)
        <div class="space-y-2 border-t border-border pt-3">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-bold uppercase tracking-wide text-muted-foreground">Upload new photos</p>
                <x-ui.button type="button" variant="secondary" size="sm" class="touch-manipulation" data-photo-upload-open aria-controls="{{ $componentId }}-dialog" aria-haspopup="dialog">
                    Upload
                </x-ui.button>
            </div>

            <input type="hidden" name="photo" value="" data-photo-base64-input>
            <div data-photo-base64-list></div>
            <p data-photo-upload-status class="hidden rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground"></p>
            <p data-photo-upload-error class="hidden rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive"></p>
        </div>

        <div
            id="{{ $componentId }}-dialog"
            data-photo-upload-dialog
            aria-hidden="true"
            hidden
            class="fixed inset-0 z-50 hidden items-center justify-center bg-background/85 p-4"
        >
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="{{ $componentId }}-dialog-title"
                aria-describedby="{{ $componentId }}-dialog-description"
                class="max-h-[calc(100vh-2rem)] w-full max-w-3xl overflow-y-auto rounded border border-border bg-card p-0 text-card-foreground"
            >
                <div class="space-y-4 p-4">
                    <div class="flex items-start justify-between gap-3 border-b border-border pb-3">
                        <div class="space-y-1">
                            <h3 id="{{ $componentId }}-dialog-title" class="text-lg font-bold text-foreground">{{ $uploadTitle }}</h3>
                            <p id="{{ $componentId }}-dialog-description" class="text-sm text-muted-foreground">{{ $uploadHelp }}</p>
                        </div>

                        <x-ui.button type="button" variant="secondary" size="sm" data-photo-upload-close>
                            Close
                        </x-ui.button>
                    </div>

                    <div class="space-y-2">
                        <label for="{{ $componentId }}-files" class="block text-sm font-bold text-foreground">Choose files</label>
                        <input
                            id="{{ $componentId }}-files"
                            type="file"
                            accept="image/webp,image/png,image/jpeg"
                            multiple
                            data-photo-file-input
                            class="block w-full rounded border border-input bg-background px-3 py-2 text-sm text-foreground file:mr-3 file:rounded file:border file:border-border file:bg-secondary file:px-3 file:py-1 file:text-xs file:font-bold file:text-secondary-foreground"
                        >
                    </div>

                    <section data-photo-preview-panel class="hidden space-y-3 rounded border border-border bg-card p-4">
                        <h4 class="text-sm font-bold text-foreground">Selected uploads</h4>
                        <div data-photo-preview-grid class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3"></div>
                    </section>

                    <div class="flex justify-end border-t border-border pt-3">
                        <x-ui.button type="button" variant="secondary" data-photo-upload-close>Done</x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <input type="hidden" name="{{ $mainPickName }}" value="{{ $resolvedMainPick }}" data-photo-main-pick>
    @if(is_string($legacyMainIdName) && $legacyMainIdName !== '')
        <input type="hidden" name="{{ $legacyMainIdName }}" value="{{ $legacyMainId }}" data-photo-legacy-main-id>
    @endif

    @error($photoIdsName)
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
    @error($photoIdsName.'.*')
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
    @error($mainPickName)
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
    @if(is_string($legacyMainIdName) && $legacyMainIdName !== '')
        @error($legacyMainIdName)
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    @endif
    @error('photo_id')
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
    @error('photo')
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
    @error('photos')
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
    @error('photos.*')
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
</section>
