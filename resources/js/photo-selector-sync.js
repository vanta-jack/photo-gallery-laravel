/**
 * Photo Selector Sync
 * 
 * Keeps the cover photo selector in sync with the photo_ids checkboxes.
 * When photos are checked/unchecked, the cover selector updates to show
 * only photos that are actually selected in the album.
 */

document.addEventListener('DOMContentLoaded', function() {
    const photoCheckboxes = document.querySelectorAll('input[name="photo_ids[]"]');
    
    if (photoCheckboxes.length === 0) return;
    
    // Function to update available cover photos
    function updateCoverPhotos() {
        const selectedIds = new Set();
        const selectedPhotos = [];
        
        // Collect checked photo IDs
        photoCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedIds.add(checkbox.value);
            }
        });
        
        // Find all photo thumbnails in the photo selector
        const photoLabels = document.querySelectorAll('[role="radio"]');
        
        photoLabels.forEach(label => {
            const radio = label.querySelector('input[type="radio"]');
            if (radio) {
                if (selectedIds.has(radio.value)) {
                    // Photo is selected - show it
                    label.style.display = '';
                    label.style.opacity = '1';
                    label.style.pointerEvents = 'auto';
                } else {
                    // Photo is not selected - hide it
                    label.style.display = 'none';
                    label.style.opacity = '0.5';
                    label.style.pointerEvents = 'none';
                }
            }
        });
        
        // Clear cover photo selection if it's not in selected photos
        const coverRadio = document.querySelector('input[name="cover_photo_id"]:checked');
        if (coverRadio && !selectedIds.has(coverRadio.value)) {
            coverRadio.checked = false;
        }
    }
    
    // Listen for changes in photo checkboxes
    photoCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCoverPhotos);
    });
    
    // Initial update on page load
    updateCoverPhotos();
});
