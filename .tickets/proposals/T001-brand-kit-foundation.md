# T001: VANITI FAIRE Brand Kit Foundation

**Priority:** High (blocks all visual work)  
**Type:** Feature  
**Estimated Effort:** Large

## Summary

Implement the complete VANITI FAIRE design system including CSS variables, Inter font, dark mode, layout structure, and VNT logo lockup per the brand kit specification.

## Current State

- `resources/css/app.css` only contains Tailwind imports (`@import 'tailwindcss'`) and minimal theme config
- `resources/views/layouts/app.blade.php` uses inline `<style>` block with blue/gray color palette
- No `public/fonts/` directory exists
- No dark mode support
- No layout partials (header/footer)

## Requirements

> [!important]
> The app must prioritize mobile-first responsive implementation for the UI. Ensure that dark mode and light mode stay persistent when user switches from one view to another.

From `.tickets/active/004-site-implementations.md`:
- Inter font (400, 700 weights) self-hosted
- CSS variables for light/dark zinc color palette
- 2px border radius universally
- No shadows anywhere
- VNT logo lockup in footer only (not header)
- Dark mode default, toggleable

## Implementation Steps

### 1. Download and Place Inter Font Files

Download from google-webfonts-helper.vercel.app:
- `inter-400.woff2` (regular)
- `inter-700.woff2` (bold)

Place in: `public/fonts/inter/`

### 2. Update `resources/css/app.css`

Replace contents with:

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

/* Inter font declarations */
@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('/fonts/inter/inter-400.woff2') format('woff2');
}

@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url('/fonts/inter/inter-700.woff2') format('woff2');
}

/* Light mode colors (default in :root for fallback) */
:root {
  --color-background: #FAFAFA;
  --color-foreground: #09090B;
  --color-card: #FFFFFF;
  --color-card-foreground: #09090B;
  --color-border: #E4E4E7;
  --color-input: #E4E4E7;
  --color-primary: #18181B;
  --color-primary-foreground: #FAFAFA;
  --color-secondary: #F4F4F5;
  --color-secondary-foreground: #18181B;
  --color-muted: #F4F4F5;
  --color-muted-foreground: #71717A;
  --color-destructive: #DC2626;
  --color-ring: #09090B;
  --radius: 2px;
}

/* Dark mode colors */
.dark {
  --color-background: #09090B;
  --color-foreground: #FAFAFA;
  --color-card: #18181B;
  --color-card-foreground: #FAFAFA;
  --color-border: #27272A;
  --color-input: #27272A;
  --color-primary: #FAFAFA;
  --color-primary-foreground: #18181B;
  --color-secondary: #27272A;
  --color-secondary-foreground: #FAFAFA;
  --color-muted: #27272A;
  --color-muted-foreground: #A1A1AA;
  --color-destructive: #EF4444;
  --color-ring: #D4D4D8;
}

/* VNT Logo Lockup */
.vnt-logo {
  display: inline-flex;
  flex-direction: column;
  align-items: flex-start;
}

.vnt-wordmark {
  font-family: 'Inter', sans-serif;
  font-weight: 700;
  font-size: 3rem;
  letter-spacing: 0.01em;
  text-transform: uppercase;
  line-height: 1;
  color: var(--color-foreground);
}

.vnt-sub {
  font-family: 'Inter', sans-serif;
  font-weight: 700;
  font-size: 0.72rem;
  text-transform: uppercase;
  color: var(--color-foreground);
  width: 100%;
  display: block;
  letter-spacing: 0.43em;
}

/* Tailwind 4 theme extensions */
@theme {
  --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
  
  --color-background: var(--color-background);
  --color-foreground: var(--color-foreground);
  --color-card: var(--color-card);
  --color-card-foreground: var(--color-card-foreground);
  --color-border: var(--color-border);
  --color-input: var(--color-input);
  --color-primary: var(--color-primary);
  --color-primary-foreground: var(--color-primary-foreground);
  --color-secondary: var(--color-secondary);
  --color-secondary-foreground: var(--color-secondary-foreground);
  --color-muted: var(--color-muted);
  --color-muted-foreground: var(--color-muted-foreground);
  --color-destructive: var(--color-destructive);
  --color-ring: var(--color-ring);
  
  --radius: 2px;
  --radius-sm: var(--radius);
  --radius-md: var(--radius);
  --radius-lg: var(--radius);
}
```

### 3. Refactor `resources/views/layouts/app.blade.php`

Replace with:

```blade
<!DOCTYPE html>
<html lang="en" class="{{ session('theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VANITI FAIRE — @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground font-sans antialiased min-h-screen">

    @include('layouts.partials.header')

    {{-- Flash messages --}}
    @if(session('status'))
        <div class="max-w-5xl mx-auto px-4 mt-4">
            <div class="bg-card border border-border rounded p-3 text-sm">
                {{ session('status') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-5xl mx-auto px-4 mt-4">
            <div class="bg-card border border-border rounded p-3 text-sm text-destructive">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="max-w-5xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

</body>
</html>
```

### 4. Create `resources/views/layouts/partials/header.blade.php`

```blade
<header class="bg-card border-b border-border">
    <nav class="max-w-5xl mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex gap-6">
                <a href="{{ route('home') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Home</a>
                <a href="{{ route('photos.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Photos</a>
                <a href="{{ route('albums.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Albums</a>
                <a href="{{ route('posts.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Posts</a>
                <a href="{{ route('guestbook.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Guestbook</a>
                @auth
                    <a href="{{ route('milestones.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Milestones</a>
                @endauth
            </div>
            <div class="flex gap-4 items-center">
                <form action="{{ route('theme.toggle') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-muted-foreground hover:text-foreground transition-colors duration-150">
                        Toggle Theme
                    </button>
                </form>
                @auth
                    <a href="{{ route('profile.edit') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Profile</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
```

### 5. Create `resources/views/layouts/partials/footer.blade.php`

```blade
<footer class="mt-16 py-8 border-t border-border">
    <div class="max-w-5xl mx-auto px-4 flex justify-center">
        <div class="vnt-logo">
            <span class="vnt-wordmark">VANITI FAIRE</span>
            <span class="vnt-sub">VNT GmbH</span>
        </div>
    </div>
</footer>
```

### 6. Create `app/Http/Controllers/ThemeController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Toggle between light and dark theme.
     * Stores preference in both session and cookie.
     */
    public function toggle(Request $request): RedirectResponse
    {
        $currentTheme = session('theme', 'dark');
        $newTheme = $currentTheme === 'dark' ? 'light' : 'dark';
        
        session(['theme' => $newTheme]);
        
        return back()->withCookie(
            cookie('theme', $newTheme, 60 * 24 * 365) // 1 year
        );
    }
}
```

### 7. Add Route in `routes/web.php`

```php
use App\Http\Controllers\ThemeController;

// Add after other routes
Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
```

### 8. Update Existing Views

Update all existing views to use Tailwind utility classes instead of inline styles:

**Button classes per spec:**
```blade
{{-- Primary --}}
<button class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
    Label
</button>

{{-- Secondary --}}
<button class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">
    Label
</button>

{{-- Destructive --}}
<button class="bg-destructive text-white font-bold text-sm px-4 py-2 rounded hover:opacity-90 transition-opacity duration-150">
    Label
</button>

{{-- Ghost --}}
<button class="bg-transparent text-foreground font-bold text-sm px-4 py-2 rounded hover:bg-secondary transition-colors duration-150">
    Label
</button>
```

**Card:**
```blade
<div class="bg-card text-card-foreground border border-border rounded p-4">
    <!-- content -->
</div>
```

**Input:**
```blade
<input type="text" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring placeholder:text-muted-foreground">
```

## Files to Create/Modify

| File | Action |
|------|--------|
| `public/fonts/inter/inter-400.woff2` | Create (download) |
| `public/fonts/inter/inter-700.woff2` | Create (download) |
| `resources/css/app.css` | Modify |
| `resources/views/layouts/app.blade.php` | Modify |
| `resources/views/layouts/partials/header.blade.php` | Create |
| `resources/views/layouts/partials/footer.blade.php` | Create |
| `app/Http/Controllers/ThemeController.php` | Create |
| `routes/web.php` | Modify |
| `resources/views/photos/*.blade.php` | Modify (update classes) |
| `resources/views/albums/*.blade.php` | Modify (update classes) |
| `resources/views/posts/*.blade.php` | Modify (update classes) |
| `resources/views/guestbook/*.blade.php` | Modify (update classes) |
| `resources/views/users/*.blade.php` | Modify (update classes) |
| `resources/views/milestones/*.blade.php` | Modify (update classes) |
| `resources/views/dashboard.blade.php` | Modify (update classes) |

## Acceptance Criteria

- [ ] Inter font loads correctly (check Network tab)
- [ ] Dark mode is default when no preference set
- [ ] Theme toggle button switches between light/dark
- [ ] Theme preference persists across page loads (session)
- [ ] All colors match VANITI FAIRE spec (zinc scale)
- [ ] VNT logo appears in footer only, not header
- [ ] No shadows anywhere in the UI
- [ ] 2px border radius on all interactive elements
- [ ] All existing views updated to use new utility classes

## Dependencies

None - this is a foundation ticket.

## Blocks

- T004 (Slide Mode)
- T005 (Splash Page)
- T006 (Guestbook Feed)
- T008 (About Me Page)
- T010 (Admin Dashboard)
- T012 (AI Generator)
- T014 (Photo Analytics)
