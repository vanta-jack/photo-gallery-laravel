const ALLOWED_MIME_TYPES = new Set([
  'image/webp',
  'image/png',
  'image/jpeg',
  'image/jpg',
]);

const readAsDataUrl = (file) => new Promise((resolve, reject) => {
  const reader = new FileReader();
  reader.onload = (event) => resolve(event.target?.result);
  reader.onerror = () => reject(new Error('Failed to read image file.'));
  reader.readAsDataURL(file);
});

const createPreviewCard = ({ id, file, previewUrl }, removePhoto) => {
  const card = document.createElement('article');
  card.className = 'border border-border rounded p-2 bg-card text-card-foreground space-y-2';

  const image = document.createElement('img');
  image.src = previewUrl;
  image.alt = file.name;
  image.className = 'w-full h-24 object-cover rounded';

  const footer = document.createElement('div');
  footer.className = 'flex items-start justify-between gap-2';

  const textWrap = document.createElement('div');
  textWrap.className = 'min-w-0';

  const name = document.createElement('p');
  name.className = 'text-xs font-bold text-foreground truncate';
  name.textContent = file.name;

  const meta = document.createElement('p');
  meta.className = 'text-[11px] text-muted-foreground';
  meta.textContent = `${Math.max(1, Math.round(file.size / 1024))} KB`;

  const removeButton = document.createElement('button');
  removeButton.type = 'button';
  removeButton.className = 'bg-secondary text-secondary-foreground border border-border rounded px-2 py-1 text-[11px] font-bold hover:opacity-90 transition-opacity duration-150';
  removeButton.textContent = 'Remove';
  removeButton.dataset.photoPreviewRemove = id;
  removeButton.addEventListener('click', () => removePhoto(id));

  textWrap.append(name, meta);
  footer.append(textWrap, removeButton);
  card.append(image, footer);

  return card;
};

document.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('[data-photo-base64-form]');

  forms.forEach((form) => {
    const fileInput = form.querySelector('[data-photo-file-input]');
    const singleHiddenInput = form.querySelector('[data-photo-base64-input]');
    const hiddenList = form.querySelector('[data-photo-base64-list]');
    const status = form.querySelector('[data-photo-upload-status]');
    const error = form.querySelector('[data-photo-upload-error]');
    const previewPanel = form.querySelector('[data-photo-preview-panel]');
    const previewGrid = form.querySelector('[data-photo-preview-grid]');
    const submitButton = form.querySelector('[data-photo-submit-button]') ?? form.querySelector('button[type="submit"]');

    if (!fileInput || !singleHiddenInput || !hiddenList || !status || !error || !previewPanel || !previewGrid) {
      return;
    }

    let isProcessing = false;
    let selectedPhotos = [];
    let currentPhotoId = 0;

    const setStatus = (message) => {
      if (!message) {
        status.textContent = '';
        status.classList.add('hidden');
        return;
      }

      status.textContent = message;
      status.classList.remove('hidden');
    };

    const setError = (message) => {
      if (!message) {
        error.textContent = '';
        error.classList.add('hidden');
        return;
      }

      error.textContent = message;
      error.classList.remove('hidden');
    };

    const clearHiddenInputs = () => {
      singleHiddenInput.value = '';
      singleHiddenInput.disabled = false;
      hiddenList.innerHTML = '';
    };

    const removePhoto = (id) => {
      const photo = selectedPhotos.find((entry) => entry.id === id);

      if (photo) {
        URL.revokeObjectURL(photo.previewUrl);
      }

      selectedPhotos = selectedPhotos.filter((entry) => entry.id !== id);
      renderPreviewGrid();
    };

    const renderPreviewGrid = () => {
      previewGrid.innerHTML = '';

      if (selectedPhotos.length === 0) {
        previewPanel.classList.add('hidden');
        return;
      }

      previewPanel.classList.remove('hidden');
      selectedPhotos.forEach((photo) => {
        previewGrid.appendChild(createPreviewCard(photo, removePhoto));
      });
    };

    const setSelectedPhotos = (files) => {
      selectedPhotos.forEach((photo) => URL.revokeObjectURL(photo.previewUrl));
      selectedPhotos = files.map((file) => ({
        id: String(++currentPhotoId),
        file,
        previewUrl: URL.createObjectURL(file),
      }));
      renderPreviewGrid();
    };

    fileInput.addEventListener('change', () => {
      clearHiddenInputs();
      setError('');

      const chosenFiles = Array.from(fileInput.files ?? []);
      const invalidFiles = chosenFiles.filter((file) => !ALLOWED_MIME_TYPES.has(file.type));

      if (invalidFiles.length > 0) {
        const invalidNames = invalidFiles.map((file) => file.name).join(', ');
        setError(`Unsupported file type: ${invalidNames}`);
      }

      const validFiles = chosenFiles.filter((file) => ALLOWED_MIME_TYPES.has(file.type));
      setSelectedPhotos(validFiles);

      if (validFiles.length > 0) {
        setStatus(`${validFiles.length} image${validFiles.length === 1 ? '' : 's'} selected.`);
      } else {
        setStatus('');
      }

      fileInput.value = '';
    });

    form.addEventListener('submit', async (event) => {
      if (isProcessing) {
        event.preventDefault();
        return;
      }

      setError('');
      const requiresPhoto = form.dataset.photoRequired !== 'false';

      if (selectedPhotos.length === 0) {
        if (requiresPhoto) {
          event.preventDefault();
          setStatus('');
          setError('Please select at least one image before uploading.');
        }
        return;
      }

      event.preventDefault();
      isProcessing = true;
      const originalLabel = submitButton?.textContent;

      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
      }

      clearHiddenInputs();
      const processedPayloads = [];
      const failedFiles = [];

      for (const [index, selectedPhoto] of selectedPhotos.entries()) {
        setStatus(`Processing ${index + 1} of ${selectedPhotos.length}...`);

        try {
          const dataUrl = await readAsDataUrl(selectedPhoto.file);

          if (typeof dataUrl !== 'string') {
            failedFiles.push(selectedPhoto.file.name);
            continue;
          }

          processedPayloads.push(dataUrl);
        } catch {
          failedFiles.push(selectedPhoto.file.name);
        }
      }

      if (processedPayloads.length === 0) {
        isProcessing = false;
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = originalLabel ?? 'Upload Photo';
        }
        setStatus('');
        setError('Unable to process selected images. Please try again.');
        return;
      }

      if (processedPayloads.length === 1) {
        singleHiddenInput.disabled = false;
        singleHiddenInput.value = processedPayloads[0];
      } else {
        singleHiddenInput.disabled = true;
        processedPayloads.forEach((payload) => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'photos[]';
          input.value = payload;
          hiddenList.appendChild(input);
        });
      }

      if (failedFiles.length > 0) {
        setError(`Skipped ${failedFiles.length} file${failedFiles.length === 1 ? '' : 's'}: ${failedFiles.join(', ')}`);
      }

      setStatus('Images processed. Uploading...');
      form.submit();
    });
  });
});
