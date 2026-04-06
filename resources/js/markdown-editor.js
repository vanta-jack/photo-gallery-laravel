import EasyMDE from 'easymde';
import {
    Bold,
    Italic,
    Heading,
    Quote,
    List,
    ListOrdered,
    Link,
    Image,
    Eye,
    Columns2,
    Maximize,
    CircleHelp,
} from 'lucide';
import 'easymde/dist/easymde.min.css';

const initializedEditors = new WeakMap();

const escapeAttributeValue = (value) => String(value).replaceAll('&', '&amp;').replaceAll('"', '&quot;');

const renderLucideIcon = (iconNode) => {
    const paths = iconNode
        .map(([tag, attributes]) => {
            const attributesMarkup = Object.entries(attributes)
                .map(([key, value]) => `${key}="${escapeAttributeValue(value)}"`)
                .join(' ');

            return `<${tag} ${attributesMarkup}></${tag}>`;
        })
        .join('');

    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide-icon" aria-hidden="true" focusable="false">${paths}</svg>`;
};

const createToolbarButton = ({ name, action, title, icon, noDisable = false, noMobile = false }) => ({
    name,
    action,
    title,
    icon: renderLucideIcon(icon),
    noDisable,
    noMobile,
});

const toolbar = [
    createToolbarButton({ name: 'bold', action: EasyMDE.toggleBold, title: 'Bold', icon: Bold }),
    createToolbarButton({ name: 'italic', action: EasyMDE.toggleItalic, title: 'Italic', icon: Italic }),
    createToolbarButton({ name: 'heading', action: EasyMDE.toggleHeadingSmaller, title: 'Heading', icon: Heading }),
    '|',
    createToolbarButton({ name: 'quote', action: EasyMDE.toggleBlockquote, title: 'Quote', icon: Quote }),
    createToolbarButton({ name: 'unordered-list', action: EasyMDE.toggleUnorderedList, title: 'Generic List', icon: List }),
    createToolbarButton({ name: 'ordered-list', action: EasyMDE.toggleOrderedList, title: 'Numbered List', icon: ListOrdered }),
    '|',
    createToolbarButton({ name: 'link', action: EasyMDE.drawLink, title: 'Create Link', icon: Link }),
    createToolbarButton({ name: 'image', action: EasyMDE.drawImage, title: 'Insert Image', icon: Image }),
    '|',
    createToolbarButton({ name: 'preview', action: EasyMDE.togglePreview, title: 'Toggle Preview', icon: Eye, noDisable: true }),
    createToolbarButton({
        name: 'side-by-side',
        action: EasyMDE.toggleSideBySide,
        title: 'Toggle Side by Side',
        icon: Columns2,
        noDisable: true,
        noMobile: true,
    }),
    createToolbarButton({
        name: 'fullscreen',
        action: EasyMDE.toggleFullScreen,
        title: 'Toggle Fullscreen',
        icon: Maximize,
        noDisable: true,
        noMobile: true,
    }),
    '|',
    createToolbarButton({
        name: 'guide',
        action: 'https://www.markdownguide.org/basic-syntax/',
        title: 'Markdown Guide',
        icon: CircleHelp,
        noDisable: true,
    }),
];

const initializeMarkdownEditors = () => {
    document.querySelectorAll('textarea[data-markdown-editor]').forEach((textarea) => {
        if (initializedEditors.has(textarea) || textarea.dataset.markdownEditorInitialized === 'true') {
            return;
        }

        const editor = new EasyMDE({
            element: textarea,
            spellChecker: false,
            status: false,
            forceSync: true,
            autoDownloadFontAwesome: false,
            toolbar,
        });

        initializedEditors.set(textarea, editor);
        textarea.dataset.markdownEditorInitialized = 'true';
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMarkdownEditors);
} else {
    initializeMarkdownEditors();
}
