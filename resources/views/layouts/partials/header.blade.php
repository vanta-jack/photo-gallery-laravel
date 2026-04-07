<header class="bg-card border-b border-border">
    <nav class="max-w-5xl mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex gap-6">
                <a href="{{ route('home') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Home</a>
                <a href="{{ route('photos.analytics') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Analytics</a>
                <a href="{{ route('guestbook.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Guestbook</a>
                @auth
                    <a href="{{ route('photos.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">My Photos</a>
                    <a href="{{ route('albums.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">My Albums</a>
                    <a href="{{ route('posts.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">My Posts</a>
                    <a href="{{ route('milestones.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">My Milestones</a>
                @endauth
                @can('view-admin-dashboard')
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Admin</a>
                @endcan
            </div>
            <div class="flex gap-4 items-center">
                <button 
                    id="theme-toggle" 
                    type="button" 
                    class="inline-flex items-center gap-2 bg-secondary text-secondary-foreground font-bold text-sm px-3 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150"
                    title="Toggle theme (device/dark/light)"
                    aria-label="Toggle theme preference"
                >
                    <span>Theme</span>
                    <span id="theme-indicator" class="text-muted-foreground">Device</span>
                </button>
                @auth
                    <a href="{{ route('profile.show') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Profile</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Login</a>
                    <a href="{{ route('register') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Register</a>
                @endauth
            </div>
        </div>
    </nav>
</header>

<script>
    function createFallbackThemeManager() {
        const STORAGE_KEY = 'theme';
        const LIGHT_CLASS = 'light';
        const DARK_CLASS = 'dark';
        const htmlElement = document.documentElement;

        const readStorage = () => {
            try {
                return window.localStorage.getItem(STORAGE_KEY);
            } catch (error) {
                return null;
            }
        };

        const writeStorage = (value) => {
            try {
                if (value === null) {
                    window.localStorage.removeItem(STORAGE_KEY);
                    return;
                }

                window.localStorage.setItem(STORAGE_KEY, value);
            } catch (error) {
                // localStorage is unavailable in some browser modes; ignore and continue in-memory.
            }
        };

        const getSavedTheme = () => {
            const saved = readStorage();
            return (saved === LIGHT_CLASS || saved === DARK_CLASS) ? saved : null;
        };

        const getSystemPreference = () => {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return DARK_CLASS;
            }

            return LIGHT_CLASS;
        };

        const applyTheme = (theme) => {
            if (theme === DARK_CLASS) {
                htmlElement.classList.add(DARK_CLASS);
                htmlElement.classList.remove(LIGHT_CLASS);
                return;
            }

            htmlElement.classList.remove(DARK_CLASS);
            htmlElement.classList.add(LIGHT_CLASS);
        };

        const setTheme = (theme) => {
            writeStorage(theme);
            applyTheme(theme ?? getSystemPreference());
        };

        const listenForSystemPreferenceChanges = () => {
            if (!window.matchMedia) {
                return;
            }

            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const onSystemChange = (event) => {
                if (getSavedTheme() === null) {
                    applyTheme(event.matches ? DARK_CLASS : LIGHT_CLASS);
                }
            };

            if (typeof darkModeQuery.addEventListener === 'function') {
                darkModeQuery.addEventListener('change', onSystemChange);
                return;
            }

            if (typeof darkModeQuery.addListener === 'function') {
                darkModeQuery.addListener(onSystemChange);
            }
        };

        const manager = {
            getSavedTheme,
            getSystemPreference,
            getCurrentState() {
                return getSavedTheme() ?? 'device';
            },
            toggle() {
                const currentSaved = getSavedTheme();
                let nextTheme = null;

                if (currentSaved === null) {
                    nextTheme = DARK_CLASS;
                } else if (currentSaved === DARK_CLASS) {
                    nextTheme = LIGHT_CLASS;
                }

                setTheme(nextTheme);
                return nextTheme;
            },
            setTheme,
        };

        applyTheme(getSavedTheme() ?? getSystemPreference());
        listenForSystemPreferenceChanges();

        return manager;
    }

    function ensureThemeManager() {
        if (window.themeManager) {
            return window.themeManager;
        }

        window.themeManager = createFallbackThemeManager();
        return window.themeManager;
    }

    // Update theme indicator based on current state
    function updateThemeIndicator() {
        const indicators = {
            'device': 'Device',
            'dark': 'Dark',
            'light': 'Light',
        };

        const indicator = document.getElementById('theme-indicator');
        if (!indicator) return;

        const state = ensureThemeManager().getCurrentState();
        indicator.textContent = indicators[state] || indicators.device;
    }

    function bindThemeToggle() {
        const toggleBtn = document.getElementById('theme-toggle');
        if (!toggleBtn || toggleBtn.dataset.bound === '1') return;

        toggleBtn.dataset.bound = '1';
        toggleBtn.addEventListener('click', () => {
            ensureThemeManager().toggle();
            updateThemeIndicator();
        });
    }

    // Handle any load order by binding on DOM ready and when theme manager is ready
    document.addEventListener('DOMContentLoaded', () => {
        ensureThemeManager();
        bindThemeToggle();
        updateThemeIndicator();
    });

    window.addEventListener('theme-manager:ready', () => {
        bindThemeToggle();
        updateThemeIndicator();
    });
</script>
