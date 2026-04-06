import Cropper from 'cropperjs';
import imageCompression from 'browser-image-compression';

/**
 * Initialize image cropper on elements with [data-cropper] attribute.
 * 
 * Client-side image processing:
 * - Cropping with adjustable aspect ratio
 * - Automatic WebP conversion
 * - Compression and resizing (max 2048x2048)
 * - No backend processing needed
 * 
 * Usage in Blade:
 * <div data-cropper data-aspect-ratio="1">
 *   <input type="file" accept="image/*" />
 *   <div data-cropper-container class="hidden"></div>
 *   <input type="hidden" name="photo" data-cropper-result />
 * </div>
 */
document.addEventListener('DOMContentLoaded', () => {
  const cropperContainers = document.querySelectorAll('[data-cropper]');
  
  cropperContainers.forEach(container => {
    const fileInput = container.querySelector('input[type="file"]');
    const cropperContainer = container.querySelector('[data-cropper-container]');
    const hiddenInput = container.querySelector('[data-cropper-result]');
    const aspectRatio = container.dataset.aspectRatio 
      ? parseFloat(container.dataset.aspectRatio) 
      : 0; // 0 = free aspect ratio
    
    let cropperElement = null;
    let isSubmitting = false;
    
    if (!fileInput || !cropperContainer || !hiddenInput) {
      console.warn('Cropper container missing required elements');
      return;
    }
    
    // When user selects a file, compress and show cropper
    fileInput.addEventListener('change', async (e) => {
      const file = e.target.files[0];
      if (!file) return;
      
      // Validate it's an image
      if (!file.type.startsWith('image/')) {
        alert('Please select an image file.');
        return;
      }
      
      try {
        // Compress and resize image before cropping
        const options = {
          maxSizeMB: 10, // Max file size 10MB
          maxWidthOrHeight: 2048, // Max dimension 2048px
          useWebWorker: true,
          fileType: 'image/webp', // Convert to WebP
          initialQuality: 0.85 // 85% quality
        };
        
        const compressedFile = await imageCompression(file, options);
        
        // Convert compressed file to data URL for cropper
        const reader = new FileReader();
        reader.onload = (event) => {
          // Clear existing cropper
          cropperContainer.innerHTML = '';
          
          // Create cropper element (web component)
          cropperElement = document.createElement('cropper-canvas');
          cropperElement.setAttribute('src', event.target.result);
          if (aspectRatio > 0) {
            cropperElement.setAttribute('aspect-ratio', aspectRatio);
          }
          
          // Style the cropper
          cropperElement.style.maxWidth = '100%';
          cropperElement.style.maxHeight = '500px';
          
          cropperContainer.appendChild(cropperElement);
          cropperContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(compressedFile);
        
      } catch (error) {
        console.error('Error processing image:', error);
        alert('Failed to process image. Please try a different file.');
      }
    });
    
    // On form submit, get cropped canvas and convert to WebP
    const form = container.closest('form');
    if (form) {
      form.addEventListener('submit', async (e) => {
        if (isSubmitting) {
          return;
        }
        if (cropperElement && cropperElement.$getCroppedCanvas) {
          e.preventDefault(); // Prevent default to handle async processing
          isSubmitting = true;
          
          const canvas = cropperElement.$getCroppedCanvas({
            maxWidth: 2048,
            maxHeight: 2048,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
          });
          
          if (canvas) {
            // Convert canvas to WebP blob
            canvas.toBlob((blob) => {
              if (!blob) {
                hiddenInput.value = canvas.toDataURL('image/webp', 0.85);
                form.submit();
                return;
              }

              // Convert blob to data URL
              const reader = new FileReader();
              reader.onload = () => {
                hiddenInput.value = reader.result;
                // Now submit the form
                form.submit();
              };
              reader.readAsDataURL(blob);
            }, 'image/webp', 0.85); // WebP at 85% quality
          } else {
            isSubmitting = false;
            form.submit(); // Submit anyway if canvas fails
          }
        }
      });
    }
  });
});
