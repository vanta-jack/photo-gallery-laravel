const WEBP_MIME_TYPE = 'image/webp';
const JPEG_MIME_TYPE = 'image/jpeg';
const PNG_MIME_TYPE = 'image/png';

const supportsWebPEncoding = () => {
  const canvas = document.createElement('canvas');
  if (typeof canvas.toDataURL !== 'function') {
    return false;
  }
  return canvas.toDataURL(WEBP_MIME_TYPE).startsWith('data:image/webp;base64,');
};

const readAsDataUrl = (file) => new Promise((resolve, reject) => {
  const reader = new FileReader();
  reader.onload = (e) => resolve(e.target.result);
  reader.onerror = () => reject(new Error('Failed to read image'));
  reader.readAsDataURL(file);
});

const convertToWebP = async (file) => {
  const webpSupported = supportsWebPEncoding();
  
  // If WebP not supported, just return original file as data URL
  if (!webpSupported) {
    const dataUrl = await readAsDataUrl(file);
    return { dataUrl, mimeType: file.type };
  }

  return new Promise((resolve, reject) => {
    const img = new Image();
    
    img.onload = () => {
      const canvas = document.createElement('canvas');
      canvas.width = img.naturalWidth;
      canvas.height = img.naturalHeight;
      
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0);
      
      canvas.toBlob(
        async (blob) => {
          if (blob) {
            const reader = new FileReader();
            reader.onload = (e) => resolve({ dataUrl: e.target.result, mimeType: WEBP_MIME_TYPE });
            reader.onerror = () => reject(new Error('Failed to convert to WebP'));
            reader.readAsDataURL(blob);
          } else {
            reject(new Error('Canvas toBlob failed'));
          }
        },
        WEBP_MIME_TYPE,
        1.0
      );
    };
    
    img.onerror = () => reject(new Error('Failed to load image'));
    img.src = URL.createObjectURL(file);
  });
};

/**
 * Simple image upload with optional WebP conversion.
 * If browser supports WebP encoding, converts to WebP at full quality.
 * Otherwise, uploads original image format.
 */
document.addEventListener('DOMContentLoaded', () => {
  const uploaders = document.querySelectorAll('[data-image-uploader]');
  
  uploaders.forEach((container) => {
    const fileInput = container.querySelector('input[type="file"]');
    const hiddenInput = container.querySelector('[data-image-result]');
    const form = container.closest('form');
    
    if (!fileInput || !hiddenInput || !form) return;
    
    let isProcessing = false;
    
    form.addEventListener('submit', async (e) => {
      if (isProcessing) return;
      
      const file = fileInput.files?.[0];
      if (!file) return;
      
      e.preventDefault();
      isProcessing = true;
      
      const submitButton = form.querySelector('button[type="submit"]');
      const originalText = submitButton?.textContent || 'Upload';
      
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
      }
      
      try {
        const { dataUrl, mimeType } = await convertToWebP(file);
        hiddenInput.value = dataUrl;
        hiddenInput.dataset.mimeType = mimeType;
        form.submit();
      } catch (error) {
        isProcessing = false;
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = originalText;
        }
        alert(`Failed to process image: ${error.message}`);
      }
    });
  });
});
