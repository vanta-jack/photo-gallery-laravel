import { bindPress } from './press-handler';

const PREVIEW_DEBOUNCE_MS = 250;
const previewTimers = new WeakMap();

const insertAroundSelection = (textarea, prefix, suffix = '') => {
    const start = textarea.selectionStart ?? textarea.value.length;
    const end = textarea.selectionEnd ?? textarea.value.length;
    const selected = textarea.value.slice(start, end);
    const fallback = selected === '' ? 'text' : selected;
    const replacement = `${prefix}${fallback}${suffix}`;

    textarea.setRangeText(replacement, start, end, 'end');
    textarea.focus();
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.dispatchEvent(new Event('change', { bubbles: true }));
};

const insertPrefixSelection = (textarea, prefix, fallback = 'text') => {
    const start = textarea.selectionStart != null ? textarea.selectionStart : textarea.value.length;
    const end = textarea.selectionEnd != null ? textarea.selectionEnd : textarea.value.length;
    const selected = textarea.value.slice(start, end);
    const content = selected === '' ? fallback : selected;

    textarea.setRangeText(`${prefix}${content}`, start, end, 'end');
    textarea.focus();
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.dispatchEvent(new Event('change', { bubbles: true }));
};

const applyAction = (textarea, action) => {
    switch (action) {
        case 'bold':
            insertAroundSelection(textarea, '**', '**');
            break;
        case 'italic':
            insertAroundSelection(textarea, '*', '*');
            break;
        case 'heading':
            insertPrefixSelection(textarea, '## ', 'Heading');
            break;
        case 'quote':
            insertPrefixSelection(textarea, '> ', 'Quote');
            break;
        case 'unordered-list':
            insertPrefixSelection(textarea, '- ', 'List item');
            break;
        case 'ordered-list':
            insertPrefixSelection(textarea, '1. ', 'List item');
            break;
        case 'link':
            insertAroundSelection(textarea, '[', '](https://example.com)');
            break;
        case 'code':
            insertAroundSelection(textarea, '```\n', '\n```');
            break;
        default:
            break;
    }
};

const runToolbarAction = (event, button) => {
    const action = button.getAttribute('data-md-action');
    if (!action) {
        return;
    }

    const editor = button.closest('[data-markdown-editor]');
    if (!editor) {
        return;
    }

    const textarea = editor.querySelector('textarea[data-markdown-input]');
    if (!(textarea instanceof HTMLTextAreaElement)) {
        return;
    }

    event?.preventDefault();
    applyAction(textarea, action);
};

const dispatchPreviewUpdate = (previewId, content) => {
    if (!previewId) {
        return;
    }

    const dispatcher = window.Livewire?.dispatch;
    if (typeof dispatcher !== 'function') {
        return;
    }

    dispatcher('markdown-preview:update', { previewId, content });
};

const schedulePreviewUpdate = (textarea, previewId) => {
    const existingTimer = previewTimers.get(textarea);
    if (existingTimer) {
        window.clearTimeout(existingTimer);
    }

    const timeout = window.setTimeout(() => {
        previewTimers.delete(textarea);
        dispatchPreviewUpdate(previewId, textarea.value);
    }, PREVIEW_DEBOUNCE_MS);

    previewTimers.set(textarea, timeout);
};

const bindPreviewInput = (editor) => {
    if (!(editor instanceof HTMLElement)) {
        return;
    }

    const textarea = editor.querySelector('textarea[data-markdown-input]');
    if (!(textarea instanceof HTMLTextAreaElement)) {
        return;
    }

    const previewId = editor.dataset.markdownPreviewId ?? '';
    if (!previewId) {
        return;
    }

    const updatePreview = () => schedulePreviewUpdate(textarea, previewId);
    textarea.addEventListener('input', updatePreview);
    textarea.addEventListener('change', updatePreview);

    dispatchPreviewUpdate(previewId, textarea.value);
    if (!window.Livewire || typeof window.Livewire.dispatch !== 'function') {
        document.addEventListener('livewire:init', () => dispatchPreviewUpdate(previewId, textarea.value), { once: true });
    }
};

const bindToolbarButtons = (editor) => {
    if (!(editor instanceof HTMLElement)) {
        return;
    }

    const buttons = editor.querySelectorAll('[data-markdown-toolbar] button[data-md-action]');
    buttons.forEach((button) => {
        if (!(button instanceof HTMLButtonElement)) {
            return;
        }

        bindPress(button, (event) => runToolbarAction(event, button));
    });
};

const initializeMarkdownToolbar = () => {
    document.querySelectorAll('[data-markdown-editor]').forEach((editor) => {
        bindToolbarButtons(editor);
        bindPreviewInput(editor);
    });
};

if (!window.__markdownToolbarBound) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeMarkdownToolbar);
    } else {
        initializeMarkdownToolbar();
    }

    window.__markdownToolbarBound = true;
}
