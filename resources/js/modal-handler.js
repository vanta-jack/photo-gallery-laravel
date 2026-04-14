import { delegatePress } from './press-handler';

const resolveModalId = (trigger) => {
  if (!(trigger instanceof Element)) {
    return null;
  }

  return trigger.getAttribute('aria-controls') || trigger.getAttribute('data-modal-id');
};

const resolveModalFromTrigger = (trigger) => {
  const modalId = resolveModalId(trigger);

  if (modalId) {
    const modal = document.getElementById(modalId);
    if (modal instanceof HTMLElement && modal.hasAttribute('data-modal')) {
      return modal;
    }
  }

  const closestModal = trigger.closest('[data-modal]');
  return closestModal instanceof HTMLElement ? closestModal : null;
};

const isModalOpen = (modal) => !modal.hasAttribute('hidden') && !modal.classList.contains('hidden');

const setModalVisibility = (modal, shouldOpen) => {
  if (!(modal instanceof HTMLElement)) {
    return;
  }

  if (shouldOpen) {
    modal.removeAttribute('hidden');
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');

    const focusTarget = modal.querySelector('[data-modal-initial-focus]');
    if (focusTarget instanceof HTMLElement) {
      focusTarget.focus();
    }

    return;
  }

  modal.setAttribute('hidden', 'hidden');
  modal.classList.add('hidden');
  modal.setAttribute('aria-hidden', 'true');
};

const openModal = (modal) => {
  if (!modal || isModalOpen(modal)) {
    return;
  }

  setModalVisibility(modal, true);
};

const closeModal = (modal) => {
  if (!modal || !isModalOpen(modal)) {
    return;
  }

  setModalVisibility(modal, false);
};

delegatePress(document, '[data-modal-open]', (event, trigger) => {
  const modal = resolveModalFromTrigger(trigger);
  if (!modal) {
    return;
  }

  event.preventDefault();
  openModal(modal);
});

delegatePress(document, '[data-modal-close]', (event, trigger) => {
  const modal = resolveModalFromTrigger(trigger);
  if (!modal) {
    return;
  }

  event.preventDefault();
  closeModal(modal);
});

const handleBackdropPress = (event, isTouch) => {
  if (isTouch && event.touches && event.touches.length > 1) {
    return;
  }

  if (!(event.target instanceof Element)) {
    return;
  }

  const modal = event.target.closest('[data-modal]');
  if (!(modal instanceof HTMLElement) || event.target !== modal) {
    return;
  }

  if (!isModalOpen(modal)) {
    return;
  }

  if (isTouch) {
    modal.dataset.modalSuppressClickUntil = String(Date.now() + 500);
    if (event.cancelable) {
      event.preventDefault();
    }
  } else {
    const suppressUntil = Number(modal.dataset.modalSuppressClickUntil ?? 0);
    if (suppressUntil > Date.now()) {
      event.preventDefault();
      return;
    }
  }

  closeModal(modal);
};

document.addEventListener('touchstart', (event) => handleBackdropPress(event, true), { passive: false });
document.addEventListener('click', (event) => handleBackdropPress(event, false));

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') {
    return;
  }

  const openModals = Array.from(document.querySelectorAll('[data-modal]'))
    .filter((modal) => modal instanceof HTMLElement && isModalOpen(modal));

  if (openModals.length === 0) {
    return;
  }

  openModals.forEach((modal) => closeModal(modal));
});
