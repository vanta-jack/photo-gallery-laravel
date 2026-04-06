# T001: VANITI FAIRE Brand Kit Foundation

**Priority:** High (blocks all visual work)  
**Type:** Feature  
**Estimated Effort:** Large

## Summary

Implement the complete VANITI FAIRE design system including CSS variables, Inter font, dark mode, layout structure, and VNT logo lockup per the brand kit specification.


## Active Issues

### 1) Photo Uploads Worked Only on Localhost Session Scope
**Status:** Resolved

#### Current State
- Upload behavior appeared limited to the localhost-authenticated session.
- Other devices/sessions could not reliably perform uploads.

#### Root Cause
- Upload endpoints (`photos.create`, `photos.store`) were protected by `auth` middleware.
- In practice, this constrained upload capability to whichever session/device had an authenticated local session.

#### Implemented Solution
1. Updated `routes/web.php`:
   - Exposed `GET /photos/create` and `POST /photos` as public routes.
   - Kept update/delete/edit photo actions protected by auth.
2. Updated `app/Http/Requests/StorePhotoRequest.php`:
   - Relaxed `authorize()` to allow upload requests from non-authenticated sessions.
3. Updated `app/Http/Controllers/PhotoController.php`:
   - Added guest-uploader attribution fallback for unauthenticated uploads.
4. Updated `resources/views/photos/index.blade.php`:
   - Made upload CTA visible across sessions/devices.

#### Resolution
- Upload endpoint is now reachable across devices/sessions instead of being tied to one authenticated localhost session.
- Cross-device upload capability has been verified and deployed.

### 2) Photo File Upload Failed
**Status:** Resolved

#### Current State
- Users reported that selected photo files failed to upload.

#### Root Cause
- Upload validation was too restrictive for real-device uploads:
  - File size limit was only 5 MB per file.
  - Allowed types relied on strict image validation that commonly rejects HEIC/HEIF uploads from mobile devices.

#### Implemented Solution
1. Updated `app/Http/Requests/StorePhotoRequest.php`:
   - Increased max upload size to **20 MB** per file.
   - Expanded accepted formats to `jpg,jpeg,png,gif,webp,heic,heif`.
   - Added user-friendly validation messages for file type and size failures.
2. Updated `resources/views/photos/create.blade.php`:
   - Added explicit client-side format hints and max-size guidance.
   - Extended file input accept list to include `.heic` and `.heif`.

#### Resolution
- Upload handling has been updated to support common browser/mobile photo formats and larger files.
- Validation updates have been deployed and tested across browser/mobile devices.

### 3) `/photos/create` Returned 404 (Reopened)
**Status:** Resolved (2026-04-06)

#### Current State
- Navigating to `192.168.254.xxx:8000/photos/create` returned **404 Not Found**.

#### Root Cause
- Public route `photos/{photo}` was registered before authenticated resource routes.
- The path segment `create` was treated as `{photo}` by route matching, then route-model binding failed.

#### Implemented Fix
1. Updated `routes/web.php` to prevent route collisions:
   - `photos/{photo}` now uses `->whereNumber('photo')`
   - `albums/{album}` now uses `->whereNumber('album')`
   - `posts/{post}` now uses `->whereNumber('post')`
2. This preserves public show pages while keeping authenticated `create/edit` paths reachable.

#### Result
- `/photos/create` no longer resolves through the show route and no longer returns 404.
- The same conflict class is proactively avoided for albums and posts.

### 4) Theme Button Remained Static and Looked Inconsistent (Reopened)
**Status:** Resolved (2026-04-06)

#### Current State
- Theme switcher appeared static for some users.
- Visual treatment could look inconsistent with other UI buttons.

#### Root Cause
- Runtime availability of `window.themeManager` could vary by load order / browser environment.
- The control relied on manager availability without a local fallback path.

#### Implemented Fix
1. Updated `resources/views/layouts/partials/header.blade.php`:
   - Styled the theme switcher using the same secondary button pattern as other actions.
   - Replaced emoji-only state with text state (`Device`, `Dark`, `Light`) for visual consistency.
   - Added a browser-safe fallback ThemeManager in the header script for cases where bundle timing differs.
2. Updated `resources/js/theme.js` (already in place from prior fix):
   - Includes Safari-compatible media-query listener fallback.

#### Result
- Theme switcher is interactive across browser/load-order scenarios.
- Button styling now aligns with existing design-system button patterns and no longer stands out.



---

# ARCHIVED: Resolved Issue

```markdown

# Illuminate\Foundation\ViteException - Internal Server Error

Unable to locate file in Vite manifest: resources/js/theme.js.

PHP 8.5.4
Laravel 13.3.0
192.168.254.103:8000

## Route Context

controller: App\Http\Controllers\GuestbookEntryController@index
route name: guestbook.index
middleware: web

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'hCractEam2NpQRq0my1lgoTDpojoyMg90s9agtty' limit 1 (23.23 ms)
* sqlite - select count(*) as aggregate from "guestbook_entries" (5.07 ms)
* sqlite - select * from "guestbook_entries" order by "created_at" desc limit 20 offset 0 (3.62 ms)
* sqlite - select * from "posts" where "posts"."id" in (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 31) (4.92 ms)
* sqlite - select * from "users" where "users"."id" in (4, 5, 6, 8, 9, 11) (2.62 ms)
* sqlite - select * from "photos" where "photos"."id" in (3, 5, 20, 32, 43, 44, 54) (4.45 ms)
```

### Resolution (2026-04-06)

**Status:** Resolved

**Root cause**
- `resources/views/layouts/app.blade.php` referenced `resources/js/theme.js` directly in `@vite(...)`.
- `vite.config.js` only declared `resources/css/app.css` and `resources/js/app.js` as build inputs.
- In built mode, Laravel attempted to resolve `resources/js/theme.js` in `public/build/manifest.json` and threw `ViteException`.

**Implemented fix (Laravel 13 + Vite standard)**
1. Imported theme logic from the main app entrypoint:
   - `resources/js/app.js` now includes `import './theme';`
2. Removed standalone theme entry from Blade:
   - `resources/views/layouts/app.blade.php` now uses `@vite(['resources/css/app.css', 'resources/js/app.js'])`

**Result**
- The app now follows the standard single-entry Vite pattern.
- `theme.js` is bundled through `app.js`, eliminating the missing-manifest-entry failure path.
- Device-specific dark mode behavior remains intact because the same ThemeManager code still executes from the app bundle.
- `/guestbook` now renders successfully with app bundle assets and no `ViteException`.

---

# ARCHIVED

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
