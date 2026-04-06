@extends('layouts.app')

@section('title', 'Edit Album')

@section('content')
@php
    $hasOldInput = session()->hasOldInput();
    $selectedPhotoIds = collect($hasOldInput ? old('photo_ids', []) : $album->photos->pluck('id')->all())
        ->map(fn ($id) => (int) $id)
        ->all();

    $coverPhotoOptions = $album->photos->concat($userPhotos)->unique('id')->values();
    $selectedCoverPhotoId = $hasOldInput ? old('cover_photo_id') : $album->cover_photo_id;
@endphp

<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6">Edit Album</h1>

    <form action="{{ route('albums.update', $album) }}" method="POST" id="editAlbumForm">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-bold mb-2 text-foreground">Album Title</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $album->title) }}"
                    required
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                >
                @error('title')
                    <span class="text-destructive text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-bold mb-2 text-foreground">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    data-markdown-editor
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                >{{ old('description', $album->description) }}</textarea>
                @error('description')
                    <span class="text-destructive text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <input type="hidden" name="is_private" value="0">
                <label class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="is_private"
                        value="1"
                        {{ old('is_private', $album->is_private) ? 'checked' : '' }}
                        class="rounded border-input"
                    >
                    <span class="text-sm text-foreground">Make this album private</span>
                </label>
            </div>

            <div class="space-y-6 border-t border-border pt-6">
                <div>
                    <h2 class="text-lg font-bold text-foreground mb-1">Currently in this album</h2>
                    <p class="text-sm text-muted-foreground mb-3">Uncheck any photo to remove it.</p>

                    @if($album->photos->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($album->photos as $photo)
                                @php $isSelected = in_array($photo->id, $selectedPhotoIds, true); @endphp
                                <div class="album-photo-card relative group rounded border p-1 transition-colors duration-150" data-photo-id="{{ $photo->id }}">
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::url($photo->path) }}"
                                        alt="{{ $photo->title }}"
                                        class="w-full h-32 object-cover rounded"
                                    >
                                    <div class="absolute top-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded" data-selection-badge>
                                        {{ $isSelected ? 'Selected' : 'Not selected' }}
                                    </div>
                                    <div class="absolute top-2 right-2">
                                        <label class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="photo_ids[]"
                                                value="{{ $photo->id }}"
                                                {{ $isSelected ? 'checked' : '' }}
                                                class="album-photo-checkbox w-5 h-5 rounded border-input bg-background checked:bg-primary"
                                            >
                                        </label>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs p-1 rounded-b opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{ $photo->title }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-muted/50 border border-border rounded p-4 text-sm text-muted-foreground">
                            This album has no photos yet.
                        </div>
                    @endif
                </div>

                <div>
                    <div class="flex justify-between items-center mb-3 gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-foreground">Available from your library</h2>
                            <p class="text-sm text-muted-foreground">Select photos to add them to this album.</p>
                        </div>
                        <button
                            type="button"
                            id="openAlbumUploadModal"
                            class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150"
                        >
                            + Upload Photo
                        </button>
                    </div>

                    <p id="albumUploadSuccess" class="hidden text-xs text-emerald-600 mb-3"></p>

                    <div id="userPhotoLibraryGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 {{ $userPhotos->isEmpty() ? 'hidden' : '' }}">
                        @foreach($userPhotos as $photo)
                            @php $isSelected = in_array($photo->id, $selectedPhotoIds, true); @endphp
                            <div class="album-photo-card relative group rounded border p-1 transition-colors duration-150" data-photo-id="{{ $photo->id }}">
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::url($photo->path) }}"
                                    alt="{{ $photo->title }}"
                                    class="w-full h-32 object-cover rounded"
                                >
                                <div class="absolute top-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded" data-selection-badge>
                                    {{ $isSelected ? 'Selected' : 'Not selected' }}
                                </div>
                                <div class="absolute top-2 right-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="photo_ids[]"
                                            value="{{ $photo->id }}"
                                            {{ $isSelected ? 'checked' : '' }}
                                            class="album-photo-checkbox w-5 h-5 rounded border-input bg-background checked:bg-primary"
                                        >
                                    </label>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs p-1 rounded-b opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ $photo->title }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="userPhotoLibraryEmpty" class="bg-muted/50 border border-border rounded p-4 text-sm text-muted-foreground {{ $userPhotos->isEmpty() ? '' : 'hidden' }}">
                        No additional photos are available to add.
                    </div>
                </div>

                @error('photo_ids')
                    <span class="text-destructive text-sm block">{{ $message }}</span>
                @enderror
                @error('photo_ids.*')
                    <span class="text-destructive text-sm block">{{ $message }}</span>
                @enderror

                <div>
                    <label for="cover_photo_id" class="block text-sm font-bold mb-2 text-foreground">Cover Photo</label>
                    <select
                        id="cover_photo_id"
                        name="cover_photo_id"
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">No cover photo</option>
                        @foreach($coverPhotoOptions as $photo)
                            <option
                                value="{{ $photo->id }}"
                                {{ (string) $selectedCoverPhotoId === (string) $photo->id ? 'selected' : '' }}
                            >
                                {{ $photo->title }}
                            </option>
                        @endforeach
                    </select>
                    <p id="selectedPhotosCount" class="text-xs text-muted-foreground mt-1"></p>
                    @error('cover_photo_id')
                        <span class="text-destructive text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4 pt-6 border-t border-border">
                <button
                    type="submit"
                    class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150"
                >
                    Update Album
                </button>
                <a
                    href="{{ route('albums.show', $album) }}"
                    class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150"
                >
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<div id="albumUploadModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center px-4" role="dialog" aria-modal="true" aria-labelledby="albumUploadModalTitle">
    <div class="bg-card text-card-foreground border border-border rounded p-6 w-full max-w-lg">
        <div class="flex items-center justify-between mb-4">
            <h2 id="albumUploadModalTitle" class="text-lg font-bold text-foreground">Upload Photo</h2>
            <button type="button" id="closeAlbumUploadModal" class="text-sm text-muted-foreground hover:text-foreground">Close</button>
        </div>

        <form id="albumUploadForm" action="{{ route('albums.photos.store', $album) }}" method="POST" data-photo-ajax-form>
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="album_photo_file" class="block text-sm font-bold text-foreground mb-2">
                        Photo <span class="text-destructive">*</span>
                    </label>
                    <input
                        id="album_photo_file"
                        type="file"
                        accept="image/webp,image/png,image/jpeg"
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring cursor-pointer hover:bg-card transition-colors"
                        data-photo-file-input
                        required
                    />
                    <input type="hidden" name="photo" value="" data-photo-base64-input />
                    <p class="hidden text-xs text-muted-foreground mt-2" data-photo-upload-status></p>
                    <p class="hidden text-destructive text-sm mt-2" data-photo-upload-error></p>
                    <ul id="albumUploadFieldErrors" class="hidden text-destructive text-sm mt-2 list-disc pl-5"></ul>
                </div>

                <div>
                    <label for="album_photo_title" class="block text-sm font-bold text-foreground mb-2">Title (Optional)</label>
                    <input
                        id="album_photo_title"
                        type="text"
                        name="title"
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                    />
                </div>

                <div>
                    <label for="album_photo_description" class="block text-sm font-bold text-foreground mb-2">Description</label>
                    <textarea
                        id="album_photo_description"
                        name="description"
                        rows="3"
                        data-markdown-editor
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground"
                    ></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" id="albumUploadSubmit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                        Upload Photo
                    </button>
                    <button type="button" id="cancelAlbumUpload" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const coverPhotoSelect = document.getElementById('cover_photo_id');
    const selectedPhotosCount = document.getElementById('selectedPhotosCount');
    const userPhotoLibraryGrid = document.getElementById('userPhotoLibraryGrid');
    const userPhotoLibraryEmpty = document.getElementById('userPhotoLibraryEmpty');

    const openModalButton = document.getElementById('openAlbumUploadModal');
    const modal = document.getElementById('albumUploadModal');
    const closeModalButton = document.getElementById('closeAlbumUploadModal');
    const cancelModalButton = document.getElementById('cancelAlbumUpload');
    const modalForm = document.getElementById('albumUploadForm');
    const modalSubmitButton = document.getElementById('albumUploadSubmit');
    const uploadSuccess = document.getElementById('albumUploadSuccess');
    const modalFieldErrors = document.getElementById('albumUploadFieldErrors');

    if (!coverPhotoSelect || !selectedPhotosCount || !modal || !modalForm || !modalSubmitButton || !openModalButton || !userPhotoLibraryGrid || !userPhotoLibraryEmpty || !uploadSuccess || !modalFieldErrors) {
        return;
    }

    const fileInput = modalForm.querySelector('[data-photo-file-input]');
    const base64Input = modalForm.querySelector('[data-photo-base64-input]');
    const statusNode = modalForm.querySelector('[data-photo-upload-status]');
    const errorNode = modalForm.querySelector('[data-photo-upload-error]');

    if (!fileInput || !base64Input || !statusNode || !errorNode) {
        return;
    }

    const ALLOWED_MIME_TYPES = new Set(['image/webp', 'image/png', 'image/jpeg', 'image/jpg']);
    let isUploading = false;

    const getPhotoCheckboxes = () => Array.from(document.querySelectorAll('.album-photo-checkbox'));

    const setStatus = (message) => {
        if (!message) {
            statusNode.textContent = '';
            statusNode.classList.add('hidden');
            return;
        }

        statusNode.textContent = message;
        statusNode.classList.remove('hidden');
    };

    const setError = (message) => {
        if (!message) {
            errorNode.textContent = '';
            errorNode.classList.add('hidden');
            return;
        }

        errorNode.textContent = message;
        errorNode.classList.remove('hidden');
    };

    const clearFieldErrors = () => {
        modalFieldErrors.innerHTML = '';
        modalFieldErrors.classList.add('hidden');
    };

    const showFieldErrors = (errors) => {
        const messages = Object.values(errors).flat();

        if (!messages.length) {
            clearFieldErrors();
            return;
        }

        modalFieldErrors.innerHTML = '';

        messages.forEach((message) => {
            const item = document.createElement('li');
            item.textContent = message;
            modalFieldErrors.appendChild(item);
        });

        modalFieldErrors.classList.remove('hidden');
    };

    const readAsDataUrl = (file) => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (event) => resolve(event.target?.result);
        reader.onerror = () => reject(new Error('Failed to read image file.'));
        reader.readAsDataURL(file);
    });

    const setCardSelectionState = (checkbox) => {
        const card = checkbox.closest('.album-photo-card');

        if (!card) {
            return;
        }

        const badge = card.querySelector('[data-selection-badge]');
        const selected = checkbox.checked;

        card.classList.toggle('border-primary', selected);
        card.classList.toggle('ring-2', selected);
        card.classList.toggle('ring-primary/40', selected);
        card.classList.toggle('border-border', !selected);

        if (badge) {
            badge.textContent = selected ? 'Selected' : 'Not selected';
        }
    };

    const updateCoverPhotoOptions = () => {
        const selectedIds = new Set(
            getPhotoCheckboxes()
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value)
        );

        Array.from(coverPhotoSelect.options).forEach((option) => {
            if (option.value === '') {
                return;
            }

            option.disabled = !selectedIds.has(option.value);
        });

        if (coverPhotoSelect.value !== '' && !selectedIds.has(coverPhotoSelect.value)) {
            coverPhotoSelect.value = '';
        }

        selectedPhotosCount.textContent = `Selected photos: ${selectedIds.size}`;
    };

    const attachCheckboxListener = (checkbox) => {
        checkbox.addEventListener('change', () => {
            setCardSelectionState(checkbox);
            updateCoverPhotoOptions();
        });
    };

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        uploadSuccess.classList.add('hidden');
        uploadSuccess.textContent = '';
    };

    const closeModal = ({ resetForm } = { resetForm: false }) => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');

        if (resetForm) {
            modalForm.reset();
            base64Input.value = '';
            setStatus('');
            setError('');
            clearFieldErrors();
        }
    };

    const addCoverPhotoOption = (photo) => {
        const existingOption = coverPhotoSelect.querySelector(`option[value="${photo.id}"]`);

        if (existingOption) {
            existingOption.textContent = photo.title;
            existingOption.disabled = false;
            return;
        }

        const option = document.createElement('option');
        option.value = String(photo.id);
        option.textContent = photo.title;
        coverPhotoSelect.appendChild(option);
    };

    const buildPhotoCard = (photo) => {
        const card = document.createElement('div');
        card.className = 'album-photo-card relative group rounded border p-1 transition-colors duration-150';
        card.dataset.photoId = String(photo.id);

        card.innerHTML = `
            <img src="${photo.url}" alt="${photo.title}" class="w-full h-32 object-cover rounded">
            <div class="absolute top-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded" data-selection-badge>Selected</div>
            <div class="absolute top-2 right-2">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="photo_ids[]" value="${photo.id}" checked class="album-photo-checkbox w-5 h-5 rounded border-input bg-background checked:bg-primary">
                </label>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs p-1 rounded-b opacity-0 group-hover:opacity-100 transition-opacity">${photo.title}</div>
        `;

        return card;
    };

    const appendUploadedPhoto = (photo) => {
        const existingCard = document.querySelector(`.album-photo-card[data-photo-id="${photo.id}"]`);

        if (existingCard) {
            const checkbox = existingCard.querySelector('.album-photo-checkbox');
            if (checkbox) {
                checkbox.checked = true;
                setCardSelectionState(checkbox);
                updateCoverPhotoOptions();
            }
            return;
        }

        const card = buildPhotoCard(photo);
        userPhotoLibraryGrid.prepend(card);
        userPhotoLibraryGrid.classList.remove('hidden');
        userPhotoLibraryEmpty.classList.add('hidden');

        const checkbox = card.querySelector('.album-photo-checkbox');
        if (checkbox) {
            attachCheckboxListener(checkbox);
            setCardSelectionState(checkbox);
        }

        addCoverPhotoOption(photo);
        updateCoverPhotoOptions();
    };

    getPhotoCheckboxes().forEach((checkbox) => {
        attachCheckboxListener(checkbox);
        setCardSelectionState(checkbox);
    });

    updateCoverPhotoOptions();

    openModalButton.addEventListener('click', openModal);
    closeModalButton.addEventListener('click', () => closeModal({ resetForm: true }));
    cancelModalButton.addEventListener('click', () => closeModal({ resetForm: true }));
    modal.addEventListener('click', (event) => {
        if (event.target === modal && !isUploading) {
            closeModal({ resetForm: true });
        }
    });

    fileInput.addEventListener('change', () => {
        base64Input.value = '';
        clearFieldErrors();
        setError('');

        const selectedFile = fileInput.files?.[0];

        if (!selectedFile) {
            setStatus('');
            return;
        }

        setStatus(`Selected: ${selectedFile.name}`);
    });

    modalForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (isUploading) {
            return;
        }

        setError('');
        clearFieldErrors();

        const selectedFile = fileInput.files?.[0];

        if (!selectedFile) {
            setStatus('');
            setError('Please select an image before uploading.');
            return;
        }

        if (!ALLOWED_MIME_TYPES.has(selectedFile.type)) {
            setStatus('');
            setError('Please select a WebP, PNG, or JPEG image.');
            return;
        }

        isUploading = true;
        modalSubmitButton.disabled = true;
        modalSubmitButton.textContent = 'Uploading...';
        setStatus('Processing image...');

        try {
            const dataUrl = await readAsDataUrl(selectedFile);

            if (typeof dataUrl !== 'string') {
                throw new Error('Invalid image data.');
            }

            base64Input.value = dataUrl;
            setStatus('Uploading image...');

            const response = await fetch(modalForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: new FormData(modalForm),
            });

            const payload = await response.json();

            if (!response.ok) {
                if (response.status === 422 && payload.errors) {
                    showFieldErrors(payload.errors);
                    setError('Please correct the highlighted errors and try again.');
                } else {
                    setError(payload.message ?? 'Photo upload failed. Please try again.');
                }

                return;
            }

            appendUploadedPhoto(payload.photo);
            uploadSuccess.textContent = 'Photo uploaded and selected. You can save album changes now.';
            uploadSuccess.classList.remove('hidden');
            closeModal({ resetForm: true });
        } catch {
            setStatus('');
            setError('Unable to upload this image right now. Please try again.');
        } finally {
            isUploading = false;
            modalSubmitButton.disabled = false;
            modalSubmitButton.textContent = 'Upload Photo';
        }
    });
})();
</script>
@endsection
