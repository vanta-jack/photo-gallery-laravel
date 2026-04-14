@props([
    'name' => 'photo_id',
    'label' => 'Photo',
    'help' => null,
    'availablePhotos' => [],
    'selectedPhotoId' => null,
    'allowClear' => true,
    'required' => false,
])

@php
$componentId = 'photo-selector-single-'.uniqid();
$existingPhotos = collect($availablePhotos);
$resolvedSelectedId = is_numeric($selectedPhotoId) ? (int) $selectedPhotoId : null;
$showClearButton = $allowClear && $resolvedSelectedId !== null;
@endphp

<div {{ $attributes->class(['space-y-3 rounded border border-border bg-card p-4']) }}>
    <div class="space-y-1">
        <label class="block text-sm font-bold text-foreground">
            {{ $label }}
            @if($required)
                <span class="text-destructive" aria-hidden="true">*</span>
            @endif
        </label>
        @if($help)
            <p class="text-xs text-muted-foreground">{{ $help }}</p>
        @endif
    </div>

    <!-- Existing Photos Selection -->
    @if($existingPhotos->isNotEmpty())
        <div class="space-y-2">
            <p class="text-xs font-bold uppercase tracking-wide text-muted-foreground">Select existing photo</p>

            @if($showClearButton)
                <button
                    type="button"
                    data-photo-clear="{{ $componentId }}"
                    class="inline-flex items-center gap-1 rounded border border-border bg-secondary px-2 py-1 text-xs font-bold text-secondary-foreground transition-opacity hover:opacity-80 touch-manipulation"
                >
                    <x-icon name="x" class="w-3 h-3" />
                    Clear selection
                </button>
            @endif

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach($existingPhotos as $photo)
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
                    $isSelected = $photoId === $resolvedSelectedId;
                    @endphp

                    <label class="group relative cursor-pointer" role="radio" :aria-checked="$isSelected">
                        <input
                            type="radio"
                            name="{{ $name }}"
                            value="{{ $photoId }}"
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
        </div>
    @endif

    <!-- Upload Section -->
    <div class="space-y-2 border-t border-border pt-3">
        <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-bold uppercase tracking-wide text-muted-foreground">Upload new photo</p>
            <x-ui.button
                type="button"
                variant="secondary"
                size="sm"
                class="touch-manipulation"
                data-photo-upload-open="{{ $componentId }}"
                aria-controls="{{ $componentId }}-dialog"
                aria-haspopup="dialog"
            >
                Upload
            </x-ui.button>
        </div>

        <input type="hidden" name="{{ $name }}-base64" value="" data-photo-base64-input="{{ $componentId }}">
        <div data-photo-base64-list="{{ $componentId }}"></div>
        <p data-photo-upload-status="{{ $componentId }}" class="hidden rounded border border-border bg-secondary px-3 py-2 text-xs font-bold text-secondary-foreground"></p>
        <p data-photo-upload-error="{{ $componentId }}" class="hidden rounded border border-destructive px-3 py-2 text-xs font-bold text-destructive"></p>
    </div>

    <!-- Upload Modal -->
    <div
        id="{{ $componentId }}-dialog"
        data-photo-upload-dialog="{{ $componentId }}"
        aria-hidden="true"
        hidden
        class="fixed inset-0 z-50 hidden items-center justify-center bg-background/85 p-4"
    >
        <div
            role="dialog"
            aria-modal="true"
            aria-labelledby="{{ $componentId }}-dialog-title"
            aria-describedby="{{ $componentId }}-dialog-description"
            class="max-h-[calc(100vh-2rem)] w-full max-w-2xl overflow-y-auto rounded border border-border bg-card p-0 text-card-foreground"
        >
            <div class="space-y-4 p-4">
                <div class="flex items-start justify-between gap-3 border-b border-border pb-3">
                    <div class="space-y-1">
                        <h3 id="{{ $componentId }}-dialog-title" class="text-lg font-bold text-foreground">Upload photo</h3>
                        <p id="{{ $componentId }}-dialog-description" class="text-sm text-muted-foreground">Supported formats: WebP, PNG, JPEG.</p>
                    </div>

                    <x-ui.button
                        type="button"
                        variant="secondary"
                        size="sm"
                        data-photo-upload-close="{{ $componentId }}"
                    >
                        Close
                    </x-ui.button>
                </div>

                <div class="space-y-2">
                    <label for="{{ $componentId }}-files" class="block text-sm font-bold text-foreground">Choose file</label>
                    <input
                        id="{{ $componentId }}-files"
                        type="file"
                        accept="image/webp,image/png,image/jpeg"
                        data-photo-file-input="{{ $componentId }}"
                        class="block w-full rounded border border-input bg-background px-3 py-2 text-sm text-foreground file:mr-3 file:rounded file:border file:border-border file:bg-secondary file:px-3 file:py-1 file:text-xs file:font-bold file:text-secondary-foreground"
                    >
                </div>

                <section data-photo-preview-panel="{{ $componentId }}" class="hidden space-y-3 rounded border border-border bg-card p-4">
                    <h4 class="text-sm font-bold text-foreground">Preview</h4>
                    <div data-photo-preview-grid="{{ $componentId }}" class="flex justify-center"></div>
                </section>

                <div class="flex justify-end border-t border-border pt-3">
                    <x-ui.button type="button" variant="secondary" data-photo-upload-close="{{ $componentId }}">Done</x-ui.button>
                </div>
            </div>
        </div>
    </div>

    @if($existingPhotos->isEmpty())
        <p class="text-sm text-muted-foreground">No existing photos available. You can upload a new one below.</p>
    @endif

    @error($name)
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const componentId = '{{ $componentId }}';

        // Clear button handler
        const clearBtn = document.querySelector(`[data-photo-clear="${componentId}"]`);
        if (clearBtn) {
            clearBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const inputs = document.querySelectorAll(`input[name="{{ $name }}"]`);
                inputs.forEach(input => {
                    input.checked = false;
                });
                clearBtn.classList.add('hidden');
            });
        }

        // Upload dialog handlers
        const openBtn = document.querySelector(`[data-photo-upload-open="${componentId}"]`);
        const dialog = document.querySelector(`#${componentId}-dialog`);
        const closeButtons = document.querySelectorAll(`[data-photo-upload-close="${componentId}"]`);
        const fileInput = document.querySelector(`[data-photo-file-input="${componentId}"]`);

        const openDialog = () => {
            dialog.hidden = false;
            dialog.classList.remove('hidden');
            dialog.setAttribute('aria-hidden', 'false');
        };

        const closeDialog = () => {
            dialog.hidden = true;
            dialog.classList.add('hidden');
            dialog.setAttribute('aria-hidden', 'true');
        };

        if (openBtn) {
            openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openDialog();
            });
        }

        closeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                closeDialog();
            });
        });

        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) {
                closeDialog();
            }
        });

        // File upload handler (reuse photo-upload.js logic for single file)
        if (fileInput) {
            fileInput.addEventListener('change', async (e) => {
                const files = Array.from(e.target.files);
                if (files.length === 0) return;

                const file = files[0]; // Single file only
                const statusEl = document.querySelector(`[data-photo-upload-status="${componentId}"]`);
                const errorEl = document.querySelector(`[data-photo-upload-error="${componentId}"]`);
                const previewPanel = document.querySelector(`[data-photo-preview-panel="${componentId}"]`);
                const previewGrid = document.querySelector(`[data-photo-preview-grid="${componentId}"]`);

                // Clear previous messages
                statusEl?.classList.add('hidden');
                errorEl?.classList.add('hidden');

                try {
                    // Validate file
                    if (!['image/webp', 'image/png', 'image/jpeg'].includes(file.type)) {
                        throw new Error('Invalid file type. Please use WebP, PNG, or JPEG.');
                    }

                    const reader = new FileReader();
                    reader.onload = async (event) => {
                        const base64 = event.target.result;

                        try {
                            // Convert to blob to estimate size
                            const response = await fetch(base64);
                            const blob = await response.blob();
                            const sizeInMB = blob.size / (1024 * 1024);

                            if (sizeInMB > 10) {
                                throw new Error('File is too large. Maximum size is 10 MB.');
                            }

                            // Store base64 in hidden input
                            const base64Input = document.querySelector(`[data-photo-base64-input="${componentId}"]`);
                            if (base64Input) {
                                base64Input.value = base64;
                            }

                            // Show preview
                            previewGrid.innerHTML = '';
                            const previewImg = document.createElement('img');
                            previewImg.src = base64;
                            previewImg.alt = 'Preview';
                            previewImg.className = 'max-h-64 rounded border border-border';
                            previewGrid.appendChild(previewImg);

                            previewPanel?.classList.remove('hidden');

                            // Show status
                            if (statusEl) {
                                statusEl.textContent = 'Photo ready to upload';
                                statusEl.classList.remove('hidden');
                            }
                        } catch (err) {
                            if (errorEl) {
                                errorEl.textContent = err.message || 'Failed to process image';
                                errorEl.classList.remove('hidden');
                            }
                        }
                    };

                    reader.readAsDataURL(file);
                } catch (err) {
                    if (errorEl) {
                        errorEl.textContent = err.message || 'Failed to read file';
                        errorEl.classList.remove('hidden');
                    }
                }
            });
        }

        // Handle form submission for base64 uploads
        const form = document.closest('form', fileInput);
        if (form) {
            form.addEventListener('submit', async (e) => {
                const base64Input = document.querySelector(`[data-photo-base64-input="${componentId}"]`);
                const base64Value = base64Input?.value;

                if (base64Value) {
                    // Convert base64 to form field
                    const uploadInput = document.querySelector(`input[name="{{ $name }}"]`);
                    if (uploadInput) {
                        // Store base64 in a data attribute so the form knows it's a new upload
                        uploadInput.dataset.photoBase64 = base64Value;
                    }
                }
            });
        }
    });
</script>
