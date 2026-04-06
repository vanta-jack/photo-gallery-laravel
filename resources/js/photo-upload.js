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

document.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('[data-photo-base64-form]');

  forms.forEach((form) => {
    const fileInput = form.querySelector('[data-photo-file-input]');
    const hiddenInput = form.querySelector('[data-photo-base64-input]');
    const status = form.querySelector('[data-photo-upload-status]');
    const error = form.querySelector('[data-photo-upload-error]');
    const submitButton = form.querySelector('button[type="submit"]');

    if (!fileInput || !hiddenInput || !status || !error) {
      return;
    }

    let isProcessing = false;

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

    fileInput.addEventListener('change', () => {
      hiddenInput.value = '';
      setError('');

      const selectedFile = fileInput.files?.[0];

      if (selectedFile) {
        setStatus(`Selected: ${selectedFile.name}`);
      } else {
        setStatus('');
      }
    });

    form.addEventListener('submit', async (event) => {
      if (isProcessing) {
        event.preventDefault();
        return;
      }

      setError('');
      const selectedFile = fileInput.files?.[0];

      if (!selectedFile) {
        if (!hiddenInput.value) {
          event.preventDefault();
          setStatus('');
          setError('Please select an image before uploading.');
        }

        return;
      }

      if (!ALLOWED_MIME_TYPES.has(selectedFile.type)) {
        event.preventDefault();
        setStatus('');
        setError('Please select a WebP, PNG, or JPEG image.');
        return;
      }

      event.preventDefault();
      isProcessing = true;
      const originalLabel = submitButton?.textContent;

      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
      }

      setStatus('Processing image...');

      try {
        const dataUrl = await readAsDataUrl(selectedFile);

        if (typeof dataUrl !== 'string') {
          throw new Error('Invalid image data.');
        }

        hiddenInput.value = dataUrl;
        setStatus('Image processed. Uploading...');
        form.submit();
      } catch {
        isProcessing = false;
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = originalLabel ?? 'Upload Photo';
        }

        setStatus('');
        setError('Unable to process this image. Please try another file.');
      }
    });
  });
});
