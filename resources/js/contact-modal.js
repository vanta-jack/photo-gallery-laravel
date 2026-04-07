const initializeContactModal = () => {
    const dialog = document.querySelector('[data-contact-dialog]');
    const openButton = document.querySelector('[data-contact-open]');

    if (!dialog || !openButton) {
        return;
    }

    const openDialog = () => {
        if (typeof dialog.showModal === 'function') {
            dialog.showModal();
        } else {
            dialog.setAttribute('open', 'open');
        }
    };

    const closeDialog = () => {
        if (typeof dialog.close === 'function') {
            dialog.close();
        } else {
            dialog.removeAttribute('open');
        }
    };

    openButton.addEventListener('click', () => {
        openDialog();

        const focusTarget = dialog.querySelector('[data-contact-initial-focus]');
        if (focusTarget instanceof HTMLElement) {
            focusTarget.focus();
        }
    });

    dialog.querySelectorAll('[data-contact-close]').forEach((button) => {
        button.addEventListener('click', closeDialog);
    });

    dialog.addEventListener('click', (event) => {
        const rect = dialog.getBoundingClientRect();
        const clickedInside = rect.top <= event.clientY
            && event.clientY <= rect.bottom
            && rect.left <= event.clientX
            && event.clientX <= rect.right;

        if (!clickedInside) {
            closeDialog();
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeContactModal);
} else {
    initializeContactModal();
}
