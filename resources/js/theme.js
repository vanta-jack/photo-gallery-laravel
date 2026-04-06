/**
 * Theme Management System
 * 
 * Handles device-specific dark mode with localStorage persistence.
 * - Defaults to device OS preference (prefers-color-scheme)
 * - Allows user to override preference
 * - Persists setting in localStorage across sessions
 */

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
        const saved = localStorage.getItem(this.STORAGE_KEY);
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
            localStorage.removeItem(this.STORAGE_KEY);
            const systemTheme = this.getSystemPreference();
            this.applyTheme(systemTheme);
        } else {
            // Save preference to storage
            localStorage.setItem(this.STORAGE_KEY, theme);
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
        
        // Handle changes to system preference
        darkModeQuery.addEventListener('change', (e) => {
            // Only apply if user is not overriding (savedTheme is null)
            if (this.getSavedTheme() === null) {
                const newSystemTheme = e.matches ? this.DARK_CLASS : this.LIGHT_CLASS;
                this.applyTheme(newSystemTheme);
            }
        });
    }
}

// Initialize theme manager when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themeManager = new ThemeManager();
    });
} else {
    window.themeManager = new ThemeManager();
}
