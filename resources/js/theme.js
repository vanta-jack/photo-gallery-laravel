/**
 * Theme Management System
 * 
 * Handles device-specific dark mode with localStorage persistence.
 * - Defaults to device OS preference (prefers-color-scheme)
 * - Allows user to override preference
 * - Persists setting in localStorage across sessions
 */

const safeStorage = (() => {
    const memoryStore = new Map();

    const resolveStorage = () => {
        if (typeof window === 'undefined') {
            return null;
        }

        try {
            return window.localStorage;
        } catch {
            return null;
        }
    };

    return {
        getItem(key) {
            const storage = resolveStorage();
            if (storage) {
                try {
                    return storage.getItem(key);
                } catch {
                    // Fall back to in-memory storage.
                }
            }

            return memoryStore.has(key) ? memoryStore.get(key) : null;
        },
        setItem(key, value) {
            const storage = resolveStorage();
            if (storage) {
                try {
                    storage.setItem(key, value);
                    memoryStore.set(key, value);
                    return;
                } catch {
                    // Fall back to in-memory storage.
                }
            }

            memoryStore.set(key, value);
        },
        removeItem(key) {
            const storage = resolveStorage();
            if (storage) {
                try {
                    storage.removeItem(key);
                } catch {
                    // Fall back to in-memory storage.
                }
            }

            memoryStore.delete(key);
        },
    };
})();

class ThemeManager {
    constructor() {
        this.STORAGE_KEY = 'theme';
        this.LIGHT_CLASS = 'light';
        this.DARK_CLASS = 'dark';
        this.htmlElement = document.documentElement;
        
        // Initialize theme on page load
        this.init();
        
        // Listen for system theme changes
        this.listenForSystemPreferenceChanges();
    }

    /**
     * Initialize theme on page load
     */
    init() {
        const savedTheme = this.getSavedTheme();
        const effectiveTheme = savedTheme || this.getSystemPreference();
        this.applyTheme(effectiveTheme);
    }

    /**
     * Get saved theme from localStorage
     * Returns: 'light' | 'dark' | null
     */
    getSavedTheme() {
        const saved = safeStorage.getItem(this.STORAGE_KEY);
        // Only return if explicitly set to 'light' or 'dark'
        return (saved === this.LIGHT_CLASS || saved === this.DARK_CLASS) ? saved : null;
    }

    /**
     * Get system preference using prefers-color-scheme
     * Returns: 'dark' | 'light'
     */
    getSystemPreference() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return this.DARK_CLASS;
        }
        return this.LIGHT_CLASS;
    }

    /**
     * Apply theme to HTML element
     * @param {string} theme - 'light' or 'dark'
     */
    applyTheme(theme) {
        if (theme === this.DARK_CLASS) {
            this.htmlElement.classList.add(this.DARK_CLASS);
            this.htmlElement.classList.remove(this.LIGHT_CLASS);
        } else {
            this.htmlElement.classList.remove(this.DARK_CLASS);
            this.htmlElement.classList.add(this.LIGHT_CLASS);
        }
    }

    /**
     * Toggle theme with cycle: device → dark → light → device
     */
    toggle() {
        const currentSaved = this.getSavedTheme();
        let newTheme;

        if (currentSaved === null) {
            // Currently following device, switch to dark
            newTheme = this.DARK_CLASS;
        } else if (currentSaved === this.DARK_CLASS) {
            // Currently dark, switch to light
            newTheme = this.LIGHT_CLASS;
        } else {
            // Currently light, return to device (clear storage)
            newTheme = null;
        }

        this.setTheme(newTheme);
        return newTheme;
    }

    /**
     * Set theme and persist to localStorage
     * @param {string|null} theme - 'light' | 'dark' | null (device)
     */
    setTheme(theme) {
        if (theme === null) {
            // Clear storage to follow device preference
            safeStorage.removeItem(this.STORAGE_KEY);
            const systemTheme = this.getSystemPreference();
            this.applyTheme(systemTheme);
        } else {
            // Save preference to storage
            safeStorage.setItem(this.STORAGE_KEY, theme);
            this.applyTheme(theme);
        }
    }

    /**
     * Get current theme state for display
     * Returns: 'device' | 'dark' | 'light'
     */
    getCurrentState() {
        const saved = this.getSavedTheme();
        return saved || 'device';
    }

    /**
     * Listen for system theme preference changes
     * If user is following device preference, update immediately
     */
    listenForSystemPreferenceChanges() {
        if (!window.matchMedia) return;

        const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');

        const handleSystemChange = (e) => {
            // Only apply if user is not overriding (savedTheme is null)
            if (this.getSavedTheme() === null) {
                const newSystemTheme = e.matches ? this.DARK_CLASS : this.LIGHT_CLASS;
                this.applyTheme(newSystemTheme);
            }
        };

        // Modern browsers
        if (typeof darkModeQuery.addEventListener === 'function') {
            darkModeQuery.addEventListener('change', handleSystemChange);
            return;
        }

        // Safari fallback
        if (typeof darkModeQuery.addListener === 'function') {
            darkModeQuery.addListener(handleSystemChange);
        }
    }
}

function initializeThemeManager() {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.themeManager) {
        return;
    }

    try {
        window.themeManager = new ThemeManager();
        if (typeof window.dispatchEvent === 'function' && typeof CustomEvent === 'function') {
            window.dispatchEvent(new CustomEvent('theme-manager:ready'));
        }
    } catch (error) {
        console.warn('Theme manager failed to initialize.', error);
    }
}

// Initialize theme manager when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeThemeManager);
} else {
    initializeThemeManager();
}
