document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-slideshow-root]');
  const openButton = document.querySelector('[data-slideshow-open]');

  if (!root || !openButton) {
    return;
  }

  const photoPayload = root.querySelector('[data-slideshow-photos]');
  const image = root.querySelector('[data-slideshow-image]');
  const title = root.querySelector('[data-slideshow-title]');
  const description = root.querySelector('[data-slideshow-description]');
  const counter = root.querySelector('[data-slideshow-counter]');
  const date = root.querySelector('[data-slideshow-date]');
  const detailLink = root.querySelector('[data-slideshow-detail-link]');
  const closeButton = root.querySelector('[data-slideshow-close]');
  const previousButton = root.querySelector('[data-slideshow-prev]');
  const nextButton = root.querySelector('[data-slideshow-next]');
  const stageButton = root.querySelector('[data-slideshow-stage]');
  const autoplayButton = root.querySelector('[data-slideshow-toggle-autoplay]');
  const controls = root.querySelectorAll('[data-slideshow-controls]');

  if (
    !photoPayload
    || !image
    || !title
    || !description
    || !counter
    || !date
    || !detailLink
    || !closeButton
    || !previousButton
    || !nextButton
    || !stageButton
    || !autoplayButton
  ) {
    return;
  }

  let photos = [];

  try {
    photos = JSON.parse(photoPayload.textContent ?? '[]');
  } catch {
    photos = [];
  }

  if (!Array.isArray(photos) || photos.length === 0) {
    return;
  }

  let currentIndex = 0;
  let isOpen = false;
  let controlsVisible = true;
  let autoplayTimer = null;
  let touchStartX = 0;
  let touchStartY = 0;

  const stopAutoplay = () => {
    if (autoplayTimer !== null) {
      window.clearInterval(autoplayTimer);
      autoplayTimer = null;
    }

    autoplayButton.textContent = 'Play';
  };

  const startAutoplay = () => {
    stopAutoplay();
    autoplayButton.textContent = 'Pause';
    autoplayTimer = window.setInterval(() => {
      currentIndex = (currentIndex + 1) % photos.length;
      renderCurrentPhoto();
    }, 3500);
  };

  const setControlsVisibility = (visible) => {
    controlsVisible = visible;
    controls.forEach((control) => {
      control.classList.toggle('hidden', !visible);
    });
  };

  const renderCurrentPhoto = () => {
    const currentPhoto = photos[currentIndex];

    image.src = currentPhoto.url;
    image.alt = currentPhoto.title || 'Slideshow photo';
    title.textContent = currentPhoto.title || 'Photo';
    if (typeof currentPhoto.description_html === 'string' && currentPhoto.description_html.trim() !== '') {
      description.innerHTML = currentPhoto.description_html;
    } else {
      description.textContent = 'No description provided.';
    }
    counter.textContent = `${currentIndex + 1} / ${photos.length}`;
    date.textContent = currentPhoto.created_at || '';
    detailLink.href = currentPhoto.show_url || '#';
  };

  const openSlideshow = () => {
    isOpen = true;
    root.classList.remove('hidden');
    root.setAttribute('aria-hidden', 'false');
    setControlsVisibility(true);
    renderCurrentPhoto();
    document.body.classList.add('overflow-hidden');
  };

  const closeSlideshow = () => {
    isOpen = false;
    stopAutoplay();
    root.classList.add('hidden');
    root.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
  };

  const goToNext = () => {
    currentIndex = (currentIndex + 1) % photos.length;
    renderCurrentPhoto();
  };

  const goToPrevious = () => {
    currentIndex = (currentIndex - 1 + photos.length) % photos.length;
    renderCurrentPhoto();
  };

  openButton.addEventListener('click', openSlideshow);
  openButton.addEventListener('touchend', openSlideshow);
  closeButton.addEventListener('click', closeSlideshow);
  nextButton.addEventListener('click', goToNext);
  previousButton.addEventListener('click', goToPrevious);
  autoplayButton.addEventListener('click', () => {
    if (autoplayTimer === null) {
      startAutoplay();
      return;
    }

    stopAutoplay();
  });

  stageButton.addEventListener('click', () => {
    setControlsVisibility(!controlsVisible);
  });

  root.addEventListener('touchstart', (event) => {
    const touch = event.touches[0];
    touchStartX = touch.clientX;
    touchStartY = touch.clientY;
  }, { passive: true });

  root.addEventListener('touchend', (event) => {
    const touch = event.changedTouches[0];
    const deltaX = touch.clientX - touchStartX;
    const deltaY = touch.clientY - touchStartY;

    if (Math.abs(deltaX) > 50 && Math.abs(deltaY) < 40) {
      if (deltaX < 0) {
        goToNext();
      } else {
        goToPrevious();
      }
      return;
    }

    if (Math.abs(deltaX) < 12 && Math.abs(deltaY) < 12) {
      setControlsVisibility(!controlsVisible);
    }
  }, { passive: true });

  document.addEventListener('keydown', (event) => {
    if (!isOpen) {
      return;
    }

    if (event.key === 'Escape') {
      event.preventDefault();
      closeSlideshow();
      return;
    }

    if (event.key === 'ArrowRight') {
      event.preventDefault();
      goToNext();
      return;
    }

    if (event.key === 'ArrowLeft') {
      event.preventDefault();
      goToPrevious();
      return;
    }

    if (event.key === ' ') {
      event.preventDefault();
      if (autoplayTimer === null) {
        startAutoplay();
      } else {
        stopAutoplay();
      }
    }
  });
});
