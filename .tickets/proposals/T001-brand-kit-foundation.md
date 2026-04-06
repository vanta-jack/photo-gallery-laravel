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

## Active Issues

```markdown
# Illuminate\Foundation\ViteException - Internal Server Error

Unable to locate file in Vite manifest: resources/js/theme.js.

PHP 8.5.4
Laravel 13.3.0
192.168.254.103:8000

## Stack Trace

0 - vendor/laravel/framework/src/Illuminate/Foundation/Vite.php:999
1 - vendor/laravel/framework/src/Illuminate/Foundation/Vite.php:390
2 - resources/views/layouts/app.blade.php:7
3 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:123
4 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:124
5 - vendor/laravel/framework/src/Illuminate/View/Engines/PhpEngine.php:57
6 - vendor/laravel/framework/src/Illuminate/View/Engines/CompilerEngine.php:76
7 - vendor/laravel/framework/src/Illuminate/View/View.php:208
8 - vendor/laravel/framework/src/Illuminate/View/View.php:191
9 - vendor/laravel/framework/src/Illuminate/View/View.php:160
10 - resources/views/guestbook/index.blade.php:35
11 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:123
12 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:124
13 - vendor/laravel/framework/src/Illuminate/View/Engines/PhpEngine.php:57
14 - vendor/laravel/framework/src/Illuminate/View/Engines/CompilerEngine.php:76
15 - vendor/laravel/framework/src/Illuminate/View/View.php:208
16 - vendor/laravel/framework/src/Illuminate/View/View.php:191
17 - vendor/laravel/framework/src/Illuminate/View/View.php:160
18 - vendor/laravel/framework/src/Illuminate/Http/Response.php:78
19 - vendor/laravel/framework/src/Illuminate/Http/Response.php:34
20 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:939
21 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:906
22 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
23 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
24 - vendor/laravel/boost/src/Middleware/InjectBoost.php:22
25 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
26 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:52
27 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
28 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestForgery.php:104
29 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
30 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
31 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
32 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
33 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
34 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
35 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
36 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
37 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
38 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
39 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
40 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
41 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
42 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
43 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
44 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
45 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
46 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
47 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
48 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
49 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
50 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
51 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
52 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
53 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
54 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
55 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
56 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
57 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
58 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
59 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
60 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
61 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
62 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:28
63 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
64 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
65 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
66 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
67 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
68 - public/index.php:20
69 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23


## Request

GET /guestbook

## Headers

* **host**: 192.168.254.103:8000
* **user-agent**: Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1
* **upgrade-insecure-requests**: 1
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **priority**: u=0, i
* **accept-encoding**: gzip, deflate
* **cookie**: XSRF-TOKEN=eyJpdiI6Ik9ZWkRHdHNIb0FjSkxlNm8xSzA2dkE9PSIsInZhbHVlIjoiZHY2Myt1RDRIc0wvci9MbnhtZXd2N1kzYk1Vck9pa3RnYVVkK0RhcC9CTDJIa2ZmcCs2bHl0KzVMV21kbkt1bU5WYWxrcnllTytmTnN6Y3ZsRGhVc1JnYTE1dEtyTW8vSnFGdDR5aERydmc2ck9zcHVoZndwdlczNmh3WlZyeFEiLCJtYWMiOiI5MjQ1MTgyMmRhZGRmYTg4ODY2YTdiNTI3YTc4NWU1ZDk0ODgxOTk0NGFlNDQwY2Y0M2U1NzM1NGFkZWM1MmU4IiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6IklubCtvUGRDa2FaQUp1a3F1ZFlaY0E9PSIsInZhbHVlIjoiNUF6Q0xpVEZpLzNITnkxM0IyOVNWSWlGTG9DOHRiblRuZDc2L0tlUi93MEIxdmMwTlkzVitLWnQ0RGU5L2FpYXJ3MHVGcGMvNmhGYnhQdUd5cTY5TzZOckQ1WHVFc3NRRGFIY2pLMkNQemJkZ0tmdnpyaVNrTUdlc0YzYTd4UnMiLCJtYWMiOiJlZTg1ZTQ4ZDI0YmFmMmNlZGFkMDFkZTEwNmM2ODFkYmMzMDk4NTk4MWI5ZWI0N2RjYTk4YzhjNmUyNzRlOWM3IiwidGFnIjoiIn0%3D; theme=eyJpdiI6IklqdjVPWTcyRmRraU1HUEZTdmZQb1E9PSIsInZhbHVlIjoiWExEZnc5QVNKTlh5WWtpRkhSN3lFbzZZU0FqeXlCY3lOTTduYkVyZlZBd3FhMGR1RWJmQzVuZE1aUVJpUWl6YyIsIm1hYyI6IjYwNjU0MzlkOGNkMjQ5NjlhOTdkYzEyMDU1M2ZmMWZiZjI2NjUxNTgzYmQ4MmE1OGY3ZDRjNDVlNWY1OTI3ZjgiLCJ0YWciOiIifQ%3D%3D
* **connection**: keep-alive

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
