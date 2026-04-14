const DEFAULT_SUPPRESS_MS = 500;

export const bindPress = (element, handler, options = {}) => {
  if (!(element instanceof HTMLElement)) {
    return;
  }

  const suppressClickFor = Number(options.suppressClickFor ?? DEFAULT_SUPPRESS_MS);

  const suppressClickUntil = () => {
    element.dataset.pressSuppressClickUntil = String(Date.now() + suppressClickFor);
  };

  const isClickSuppressed = () => Number(element.dataset.pressSuppressClickUntil ?? 0) > Date.now();

  element.addEventListener('touchstart', (event) => {
    if (event.touches && event.touches.length > 1) {
      return;
    }

    suppressClickUntil();

    if (event.cancelable) {
      event.preventDefault();
    }

    handler(event);
  }, { passive: false });

  element.addEventListener('click', (event) => {
    if (isClickSuppressed()) {
      event.preventDefault();
      return;
    }

    handler(event);
  });
};

export const delegatePress = (root, selector, handler, options = {}) => {
  if (!(root instanceof Document || root instanceof Element)) {
    return;
  }

  const rootElement = root instanceof Document ? root.documentElement : root;
  if (!rootElement) {
    return;
  }

  const suppressClickFor = Number(options.suppressClickFor ?? DEFAULT_SUPPRESS_MS);

  const resolveMatch = (event) => {
    if (!(event.target instanceof Element)) {
      return null;
    }

    const target = event.target.closest(selector);
    if (!target || !rootElement.contains(target)) {
      return null;
    }

    return target;
  };

  const suppressClickUntil = (element) => {
    if (!(element instanceof HTMLElement)) {
      return;
    }

    element.dataset.pressSuppressClickUntil = String(Date.now() + suppressClickFor);
  };

  const isClickSuppressed = (element) => {
    if (!(element instanceof HTMLElement)) {
      return false;
    }

    return Number(element.dataset.pressSuppressClickUntil ?? 0) > Date.now();
  };

  root.addEventListener('touchstart', (event) => {
    if (event.touches && event.touches.length > 1) {
      return;
    }

    const matched = resolveMatch(event);
    if (!matched) {
      return;
    }

    suppressClickUntil(matched);

    if (event.cancelable) {
      event.preventDefault();
    }

    handler(event, matched);
  }, { passive: false });

  root.addEventListener('click', (event) => {
    const matched = resolveMatch(event);
    if (!matched) {
      return;
    }

    if (isClickSuppressed(matched)) {
      event.preventDefault();
      return;
    }

    handler(event, matched);
  });
};
