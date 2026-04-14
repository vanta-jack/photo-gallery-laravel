import { bindPress } from './press-handler';

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

const initializePhotoUploadForm = (form, formIndex) => {
  const fileInput = form.querySelector('[data-photo-file-input]');
  const singleHiddenInput = form.querySelector('[data-photo-base64-input]');
  const hiddenList = form.querySelector('[data-photo-base64-list]');
  const status = form.querySelector('[data-photo-upload-status]');
  const error = form.querySelector('[data-photo-upload-error]');
  const previewPanel = form.querySelector('[data-photo-preview-panel]');
  const previewGrid = form.querySelector('[data-photo-preview-grid]');
  const submitButton = form.querySelector('[data-photo-submit-button]') ?? form.querySelector('button[type="submit"]');
  const mainPickInput = form.querySelector('[data-photo-main-pick]');
  const legacyMainIdInput = form.querySelector('[data-photo-legacy-main-id]');

  if (!fileInput || !singleHiddenInput || !hiddenList || !status || !error || !previewPanel || !previewGrid) {
    return;
  }

  const dialog = form.querySelector('[data-photo-upload-dialog]');
  const existingCheckboxes = form.querySelectorAll('[data-photo-existing-id]');
  const existingMainRadios = form.querySelectorAll('[data-photo-main-existing]');
  const uploadMainRadioName = `photo-upload-main-${formIndex}`;

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

  const checkedExistingIds = () => Array.from(existingCheckboxes)
    .filter((checkbox) => checkbox.checked)
    .map((checkbox) => Number.parseInt(checkbox.value, 10))
    .filter((id) => Number.isInteger(id) && id > 0);

  const parseMainPick = () => {
    if (!mainPickInput || typeof mainPickInput.value !== 'string') {
      return null;
    }

    const value = mainPickInput.value.trim();
    if (!value.includes(':')) {
      return null;
    }

    const [source, rawIndex] = value.split(':', 2);
    const index = Number.parseInt(rawIndex, 10);
    if (!Number.isInteger(index) || index < 0) {
      return null;
    }

    return { source, index };
  };

  const syncMainPickInputs = () => {
    const current = parseMainPick();

    existingMainRadios.forEach((radio) => {
      const existingId = Number.parseInt(radio.value, 10);
      radio.checked = current?.source === 'existing'
        && Number.isInteger(existingId)
        && current.index === existingId
        && !radio.disabled;
    });

    previewGrid.querySelectorAll(`input[type="radio"][name="${uploadMainRadioName}"]`).forEach((radio) => {
      const uploadIndex = Number.parseInt(radio.value, 10);
      radio.checked = current?.source === 'upload'
        && Number.isInteger(uploadIndex)
        && current.index === uploadIndex;
    });
  };

  const setMainPick = (value = '') => {
    if (!mainPickInput) {
      return;
    }

    mainPickInput.value = value;

    if (legacyMainIdInput instanceof HTMLInputElement) {
      const [source, rawId] = value.split(':', 2);
      legacyMainIdInput.value = source === 'existing' && rawId ? rawId : '';
    }

    syncMainPickInputs();
  };

  const syncExistingMainInputs = () => {
    existingMainRadios.forEach((radio) => {
      const checkbox = form.querySelector(`[data-photo-existing-id="${radio.value}"]`);
      const checked = checkbox instanceof HTMLInputElement ? checkbox.checked : false;
      radio.disabled = !checked;
      if (!checked) {
        radio.checked = false;
      }
    });

    syncMainPickInputs();
  };

  const ensureMainPick = () => {
    if (!mainPickInput) {
      return;
    }

    const current = parseMainPick();
    const existingIds = checkedExistingIds();

    if (current?.source === 'existing' && existingIds.includes(current.index)) {
      syncMainPickInputs();
      return;
    }

    if (current?.source === 'upload' && current.index >= 0 && current.index < selectedPhotos.length) {
      syncMainPickInputs();
      return;
    }

    if (existingIds.length > 0) {
      setMainPick(`existing:${existingIds[0]}`);
      return;
    }

    if (selectedPhotos.length > 0) {
      setMainPick('upload:0');
      return;
    }

    setMainPick('');
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
      ensureMainPick();
      return;
    }

    previewPanel.classList.remove('hidden');

    selectedPhotos.forEach((photo, index) => {
      const card = document.createElement('article');
      card.className = 'border border-border rounded p-2 bg-card text-card-foreground space-y-2';

      const image = document.createElement('img');
      image.src = photo.previewUrl;
      image.alt = photo.file.name;
      image.className = 'w-full h-24 object-cover rounded';

      const footer = document.createElement('div');
      footer.className = 'space-y-2';

      const top = document.createElement('div');
      top.className = 'flex items-start justify-between gap-2';

      const textWrap = document.createElement('div');
      textWrap.className = 'min-w-0';

      const name = document.createElement('p');
      name.className = 'text-xs font-bold text-foreground truncate';
      name.textContent = photo.file.name;

      const meta = document.createElement('p');
      meta.className = 'text-[11px] text-muted-foreground';
      meta.textContent = `${Math.max(1, Math.round(photo.file.size / 1024))} KB`;

      const removeButton = document.createElement('button');
      removeButton.type = 'button';
      removeButton.className = 'bg-secondary text-secondary-foreground border border-border rounded px-2 py-1 text-[11px] font-bold hover:opacity-90 transition-opacity duration-150';
      removeButton.textContent = 'Remove';
      removeButton.dataset.photoPreviewRemove = photo.id;
      removeButton.addEventListener('click', () => removePhoto(photo.id));

      textWrap.append(name, meta);
      top.append(textWrap, removeButton);

      const mainLabel = document.createElement('label');
      mainLabel.className = 'inline-flex items-center gap-2 text-[11px] text-muted-foreground';

      const mainRadio = document.createElement('input');
      mainRadio.type = 'radio';
      mainRadio.name = uploadMainRadioName;
      mainRadio.value = String(index);
      mainRadio.className = 'h-4 w-4 border-input text-primary focus-visible:ring-ring';
      mainRadio.checked = parseMainPick()?.source === 'upload' && parseMainPick()?.index === index;
      mainRadio.addEventListener('change', () => {
        setMainPick(`upload:${index}`);
        existingMainRadios.forEach((radio) => {
          radio.checked = false;
        });
      });

      const mainText = document.createElement('span');
      mainText.textContent = 'Use as main image';

      mainLabel.append(mainRadio, mainText);
      footer.append(top, mainLabel);
      card.append(image, footer);
      previewGrid.appendChild(card);
    });

    ensureMainPick();
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

  const openDialog = () => {
    if (!dialog) {
      return;
    }

    if (!dialog.hasAttribute('hidden')) {
      return;
    }

    dialog.removeAttribute('hidden');
    dialog.classList.remove('hidden');
    dialog.classList.add('flex');
    dialog.setAttribute('aria-hidden', 'false');
  };

  const closeDialog = () => {
    if (!dialog) {
      return;
    }

    if (dialog.hasAttribute('hidden')) {
      return;
    }

    dialog.setAttribute('hidden', 'hidden');
    dialog.classList.remove('flex');
    dialog.classList.add('hidden');
    dialog.setAttribute('aria-hidden', 'true');
  };

  const bindDialogControl = (button, action) => {
    if (!(button instanceof HTMLElement)) {
      return;
    }

    if (button.dataset.photoUploadBound === 'true') {
      return;
    }

    button.dataset.photoUploadBound = 'true';

    bindPress(button, (event) => {
      event.preventDefault();
      action();
    });
  };

  if (dialog) {
    dialog.setAttribute('hidden', 'hidden');
    dialog.classList.remove('flex');
    dialog.classList.add('hidden');
    dialog.setAttribute('aria-hidden', 'true');

    form.querySelectorAll('[data-photo-upload-open]').forEach((button) => {
      bindDialogControl(button, openDialog);
    });

    form.querySelectorAll('[data-photo-upload-close]').forEach((button) => {
      bindDialogControl(button, closeDialog);
    });

    dialog.addEventListener('click', (event) => {
      if (event.target === dialog) {
        closeDialog();
      }
    });

    dialog.addEventListener('pointerup', (event) => {
      if (event.pointerType !== 'touch' || event.target !== dialog) {
        return;
      }

      event.preventDefault();
      closeDialog();
    });

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape' || dialog.hasAttribute('hidden')) {
        return;
      }

      closeDialog();
    });
  }

  existingCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
      syncExistingMainInputs();
      ensureMainPick();
    });
  });

  existingMainRadios.forEach((radio) => {
    radio.addEventListener('change', () => {
      if (!radio.checked) {
        return;
      }

      setMainPick(`existing:${radio.value}`);
    });
  });

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
    syncExistingMainInputs();
    ensureMainPick();

    const requiresPhoto = form.dataset.photoRequired !== 'false';
    const hasExisting = checkedExistingIds().length > 0;
    const hasUploads = selectedPhotos.length > 0;

    if (!hasUploads) {
      if (requiresPhoto && !hasExisting) {
        event.preventDefault();
        setStatus('');
        setError('Please select at least one image before submitting.');
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
        submitButton.textContent = originalLabel ?? 'Submit';
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

    setStatus('Images processed. Submitting...');
    form.submit();
  });

  syncExistingMainInputs();
  ensureMainPick();
};

const initializePhotoUpload = () => {
  const forms = document.querySelectorAll('[data-photo-base64-form]');

  forms.forEach((form, index) => {
    initializePhotoUploadForm(form, index + 1);
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializePhotoUpload);
} else {
  initializePhotoUpload();
}
