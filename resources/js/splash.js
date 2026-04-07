document.addEventListener('DOMContentLoaded', () => {
  const splash = document.querySelector('[data-splash-modal]');

  if (!splash) {
    return;
  }

  const closeButton = splash.querySelector('[data-splash-close]');
  const storageKey = 'vnt_splash_seen_v1';
  let dismissTimer = null;

  const hideSplash = () => {
    splash.classList.add('hidden');
    splash.classList.remove('flex');
    splash.setAttribute('aria-hidden', 'true');

    if (dismissTimer !== null) {
      window.clearTimeout(dismissTimer);
      dismissTimer = null;
    }
  };

  const markSeen = () => {
    try {
      window.localStorage.setItem(storageKey, '1');
    } catch {
      // Ignore storage failures and show splash again on next visit.
    }
  };

  const dismissSplash = () => {
    markSeen();
    hideSplash();
  };

  let hasSeenSplash = false;

  try {
    hasSeenSplash = window.localStorage.getItem(storageKey) === '1';
  } catch {
    hasSeenSplash = false;
  }

  if (hasSeenSplash) {
    hideSplash();
    return;
  }

  splash.classList.remove('hidden');
  splash.classList.add('flex');
  splash.setAttribute('aria-hidden', 'false');
  dismissTimer = window.setTimeout(dismissSplash, 2500);

  if (closeButton) {
    closeButton.addEventListener('click', dismissSplash);
  }
});
