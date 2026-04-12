# Blade Views & Livewire Integration Guide

A practical guide to rebuilding the photo gallery UI using modular Blade components, reusable building blocks, modal patterns, and Livewire integration.

---

## Table of Contents

1. [Folder Structure & Organization](#folder-structure)
2. [Blade Components as Building Blocks](#blade-components)
3. [Layout Architecture](#layout-architecture)
4. [Modal Patterns & Reusable Blueprints](#modal-patterns)
5. [View Composers for Shared Data](#view-composers)
6. [Livewire Integration](#livewire-integration)
7. [Migration Workflow: Blank Canvas to Full App](#migration-workflow)

---

## Folder Structure & Organization

Organize `resources/views/` to support both static Blade pages and reactive Livewire components:

```
resources/views/
├── layouts/
│   ├── app.blade.php                 # Main layout (keep as foundation)
│   └── partials/
│       ├── header.blade.php
│       ├── footer.blade.php
│       └── sidebar.blade.php         # Optional shared sidebar
│
├── components/
│   ├── ui/                           # ← Building blocks go here
│   │   ├── button.blade.php
│   │   ├── card.blade.php
│   │   ├── modal.blade.php           # ← Reusable modal blueprint
│   │   ├── form-input.blade.php
│   │   ├── form-textarea.blade.php
│   │   ├── badge.blade.php
│   │   └── alert.blade.php
│   ├── icon.blade.php                # (Already exists—keep)
│   └── splash-modal.blade.php         # (Already exists—keep)
│
├── auth/                             # Authentication views
│   ├── login.blade.php               # ← Blank canvas (ready to rebuild)
│   └── register.blade.php            # ← Blank canvas (ready to rebuild)
│
├── photos/
│   ├── index.blade.php               # ← Blank canvas
│   ├── create.blade.php              # ← Blank canvas
│   ├── edit.blade.php                # ← Blank canvas
│   ├── show.blade.php                # ← Blank canvas
│   ├── analytics.blade.php           # ← Blank canvas
│   └── partials/
│       └── slideshow-modal.blade.php # ← Blank canvas
│
├── albums/
│   ├── index.blade.php               # ← Blank canvas
│   ├── create.blade.php              # ← Blank canvas
│   ├── edit.blade.php                # ← Blank canvas
│   └── show.blade.php                # ← Blank canvas
│
├── posts/
│   ├── index.blade.php               # ← Blank canvas
│   ├── create.blade.php              # ← Blank canvas
│   ├── edit.blade.php                # ← Blank canvas
│   └── show.blade.php                # ← Blank canvas
│
├── milestones/
│   ├── index.blade.php               # ← Blank canvas
│   ├── create.blade.php              # ← Blank canvas
│   ├── edit.blade.php                # ← Blank canvas
│   └── show.blade.php                # ← Blank canvas
│
├── guestbook/
│   ├── index.blade.php               # ← Blank canvas
│   ├── create.blade.php              # ← Blank canvas
│   ├── edit.blade.php                # ← Blank canvas (newly created)
│   └── partials/
│       ├── feed-item.blade.php       # ← Blank canvas
│       └── user-avatar.blade.php     # ← Blank canvas
│
├── users/
│   ├── show.blade.php                # ← Blank canvas
│   ├── edit.blade.php                # ← Blank canvas
│   └── partials/
│       ├── contact-modal.blade.php   # ← Blank canvas
│       └── engagement-stats.blade.php # ← Blank canvas
│
├── comments/
│   ├── create.blade.php              # ← Created (blank canvas)
│   └── edit.blade.php                # ← Created (blank canvas)
│
├── ratings/
│   └── create.blade.php              # ← Created (blank canvas)
│
└── votes/
    └── create.blade.php              # ← Created (blank canvas)
```

**Rationale:**
- `components/ui/` holds reusable, composable UI primitives
- Feature folders group related views
- All page views are now blank canvases ready for custom implementation
- Shared layouts/components remain as the foundation

---

## Blade Components as Building Blocks

Create reusable, composable Blade components using Laravel's component system. All components live in `resources/views/components/ui/`.

### Basic Pattern: The UI Button Component

**File:** `resources/views/components/ui/button.blade.php`

```blade
@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $baseClasses = 'font-bold transition-opacity duration-150 inline-flex items-center gap-2 rounded border';
    
    $variantClasses = match($variant) {
        'primary' => 'bg-primary text-primary-foreground border-primary hover:opacity-90',
        'secondary' => 'bg-secondary text-secondary-foreground border-border hover:opacity-90',
        'destructive' => 'bg-destructive text-destructive-foreground border-destructive hover:opacity-90',
        default => 'bg-secondary text-secondary-foreground border-border',
    };
    
    $sizeClasses = match($size) {
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->class([$baseClasses, $variantClasses, $sizeClasses]) }}
>
    {{ $slot }}
</button>
```

**Usage:**
```blade
<!-- Primary button (default) -->
<x-ui.button>Save</x-ui.button>

<!-- Secondary button with custom class -->
<x-ui.button variant="secondary" class="w-full">Cancel</x-ui.button>

<!-- Destructive with icon -->
<x-ui.button variant="destructive" size="sm">
    <x-icon name="trash" class="w-4 h-4" />
    Delete
</x-ui.button>
```

### Pattern: The Card Container Component

**File:** `resources/views/components/ui/card.blade.php`

```blade
@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-card text-card-foreground border border-border rounded p-6']) }}>
    @if($title)
        <h2 class="text-lg font-bold text-foreground mb-4">{{ $title }}</h2>
    @endif
    
    {{ $slot }}
</div>
```

**Usage:**
```blade
<x-ui.card title="Photo Details">
    <p>Your content here</p>
</x-ui.card>

<!-- Merge custom classes -->
<x-ui.card class="shadow-lg">
    <p>With shadow</p>
</x-ui.card>
```

### Pattern: Form Input Component

**File:** `resources/views/components/ui/form-input.blade.php`

```blade
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-bold text-foreground">
            {{ $label }}
        </label>
    @endif
    
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $attributes->merge(['class' => 'w-full bg-background text-foreground border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring']) }}
    />
    
    @error($name)
        <span class="text-xs text-destructive">{{ $message }}</span>
    @enderror
</div>
```

**Usage:**
```blade
<x-ui.form-input 
    name="email" 
    type="email"
    label="Email Address"
    value="{{ old('email') }}"
/>
```

### Key Principle: `$attributes->merge()`

Every component should use `$attributes->merge()` to allow consumers to customize styling:

```blade
<!-- Component definition -->
<div {{ $attributes->merge(['class' => 'px-4 py-2 rounded']) }}>
    {{ $slot }}
</div>

<!-- Usage with extra classes -->
<x-my-component class="bg-blue-500">Content</x-my-component>

<!-- Result: class="px-4 py-2 rounded bg-blue-500" -->
```

**Attribute helpers:**
- `->merge([...])` – Merge attributes, combine classes
- `->class([...])` – Conditionally add classes
- `->prepends(...)` – Prepend defaults to data attributes

---

## Layout Architecture

The main layout orchestrates shared UI (header, footer) and provides section anchors for content.

### Main Layout

**File:** `resources/views/layouts/app.blade.php` (now with Livewire support)

```blade
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VANITI FAIRE — @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
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

    @livewireScripts

</body>
</html>
```

**Key points:**
- `@yield('title')` and `@yield('content')` are placeholders for child views
- `@livewireStyles` and `@livewireScripts` enable Livewire (already added!)
- Flash messages are handled at the layout level (not duplicated in every page)

### Child Page Layout

All page views extend the main layout:

```blade
@extends('layouts.app')

@section('title', 'Photos')

@section('content')

<x-ui.card title="My Photos">
    <p>Your page content here.</p>
</x-ui.card>

@endsection
```

---

## Modal Patterns & Reusable Blueprints

Modals are a common UI pattern. Create a reusable modal building block that works both as static Blade and with Livewire.

### Reusable Modal Blueprint Component

**File:** `resources/views/components/ui/modal.blade.php`

```blade
@props([
    'id',
    'title',
    'size' => 'md',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        default => 'max-w-md',
    };
@endphp

<div
    id="{{ $id }}"
    {{ $attributes->merge(['class' => 'fixed inset-0 z-50 hidden items-center justify-center bg-background/85 px-4', 'data-modal' => $id]) }}
    role="dialog"
    aria-modal="true"
    :aria-labelledby="'{{ $id }}-title'"
>
    <div class="w-full {{ $sizeClasses }} bg-card text-card-foreground border border-border rounded p-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <h2 id="{{ $id }}-title" class="text-lg font-bold text-foreground">
                {{ $title }}
            </h2>
            <button
                type="button"
                data-modal-close="{{ $id }}"
                class="inline-flex items-center justify-center w-6 h-6 text-muted-foreground hover:text-foreground transition-colors"
                aria-label="Close modal"
            >
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        {{ $slot }}
    </div>
</div>
```

**Usage:**

```blade
<!-- Trigger button -->
<x-ui.button data-modal-open="contact-modal">Contact Us</x-ui.button>

<!-- Modal -->
<x-ui.modal id="contact-modal" title="Contact">
    <form>
        <x-ui.form-input name="name" label="Name" />
        <x-ui.form-input name="email" type="email" label="Email" />
        <div class="flex gap-2 mt-4">
            <x-ui.button variant="primary">Send</x-ui.button>
            <x-ui.button variant="secondary" data-modal-close="contact-modal">Cancel</x-ui.button>
        </div>
    </form>
</x-ui.modal>
```

### Modal JavaScript Handler (Blade + Alpine Alternative)

Add a script to `resources/js/modal.js` to handle show/hide on all modals using `data-modal` attributes:

```javascript
document.addEventListener('DOMContentLoaded', () => {
    // Handle modal open triggers
    document.querySelectorAll('[data-modal-open]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = trigger.getAttribute('data-modal-open');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        });
    });

    // Handle modal close triggers
    document.querySelectorAll('[data-modal-close]').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = closeBtn.getAttribute('data-modal-close');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });

    // Close on backdrop click
    document.querySelectorAll('[data-modal]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });
});
```

Import this in `resources/js/app.js`:
```javascript
import './modal';
```

### Modal A11y Checklist

- ✓ Modal has `role="dialog"` and `aria-modal="true"`
- ✓ Modal title is linked with `aria-labelledby`
- ✓ Close button has proper `aria-label`
- ✓ Focus is managed (can be enhanced with Alpine or Livewire)
- ✓ Backdrop click closes modal
- ✓ Keyboard (Escape key) closes modal (enhance with JavaScript)

---

## View Composers for Shared Data

View Composers inject data into views automatically, reducing duplication across controllers.

### Example: Shared Navigation Data

**Create:** `app/View/Composers/NavigationComposer.php`

```php
<?php

namespace App\View\Composers;

use Illuminate\View\View;

class NavigationComposer
{
    public function compose(View $view): void
    {
        $view->with('navItems', [
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Analytics', 'route' => 'photos.analytics'],
            ['label' => 'Guestbook', 'route' => 'guestbook.index'],
        ]);
    }
}
```

**Register in:** `app/Providers/AppServiceProvider.php` (boot method)

```php
use App\View\Composers\NavigationComposer;
use Illuminate\Support\Facades\View;

public function boot(): void
{
    View::composer('layouts.partials.header', NavigationComposer::class);
}
```

**Use in header partial:**

```blade
<nav class="flex gap-4">
    @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}" class="hover:text-primary">
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
```

---

## Livewire Integration

Livewire enables reactive, dynamic components without writing JavaScript. Start small and adopt incrementally. **Livewire is already installed and wired into your layout!**

### Step 1: Create Your First Livewire Component

```bash
php artisan make:livewire ShowPhotos
```

This generates two files:
- `app/Livewire/ShowPhotos.php` – Component class
- `resources/views/livewire/show-photos.blade.php` – Component view

### Example: Photo Gallery Component

**File:** `app/Livewire/ShowPhotos.php`

```php
<?php

namespace App\Livewire;

use App\Models\Photo;
use Livewire\Component;

class ShowPhotos extends Component
{
    public $photos = [];
    public $search = '';

    public function mount()
    {
        $this->refreshPhotos();
    }

    public function updated($property)
    {
        if ($property === 'search') {
            $this->refreshPhotos();
        }
    }

    public function refreshPhotos()
    {
        $this->photos = Photo::where('title', 'like', "%{$this->search}%")
            ->get()
            ->toArray();
    }

    public function delete(Photo $photo)
    {
        $photo->delete();
        $this->refreshPhotos();
    }

    public function render()
    {
        return view('livewire.show-photos');
    }
}
```

**File:** `resources/views/livewire/show-photos.blade.php`

```blade
<div class="space-y-4">
    <!-- Search input -->
    <input
        type="text"
        wire:model="search"
        placeholder="Search photos..."
        class="w-full px-4 py-2 border border-input rounded"
    />

    <!-- Photos grid -->
    <div class="grid grid-cols-3 gap-4">
        @foreach($photos as $photo)
            <div class="bg-card border border-border rounded overflow-hidden">
                <img src="{{ Storage::url($photo['path']) }}" alt="{{ $photo['title'] }}" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold">{{ $photo['title'] }}</h3>
                    <button
                        wire:click="delete({{ $photo['id'] }})"
                        class="mt-2 text-sm text-destructive hover:underline"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    @if(empty($photos))
        <p class="text-muted-foreground text-center">No photos found.</p>
    @endif
</div>
```

### Using Livewire Components in Blade

Render a Livewire component using the `<livewire:` tag syntax:

```blade
<!-- In any Blade view (e.g., photos/index.blade.php) -->
@extends('layouts.app')

@section('title', 'My Photos')

@section('content')

<x-ui.card title="My Photos">
    <livewire:show-photos />
</x-ui.card>

@endsection
```

### Livewire with Modal Building Block

Combine Livewire reactivity with your modal blueprint:

**File:** `resources/views/livewire/photo-upload-modal.blade.php`

```blade
<div>
    <x-ui.button data-modal-open="upload-modal">Upload Photo</x-ui.button>

    <x-ui.modal id="upload-modal" title="Upload a Photo">
        <form wire:submit.prevent="save" class="space-y-4">
            <x-ui.form-input
                name="title"
                label="Photo Title"
                wire:model="title"
            />

            <textarea
                wire:model="description"
                placeholder="Description (optional)"
                class="w-full px-4 py-2 border border-input rounded"
            ></textarea>

            <div class="flex gap-2">
                <x-ui.button type="submit" wire:loading.attr="disabled">
                    Upload
                </x-ui.button>
                <x-ui.button type="button" variant="secondary" data-modal-close="upload-modal">
                    Cancel
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
```

**File:** `app/Livewire/PhotoUploadModal.php`

```php
<?php

namespace App\Livewire;

use App\Models\Photo;
use Livewire\Component;
use Livewire\WithFileUploads;

class PhotoUploadModal extends Component
{
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $photo = null;

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'photo' => 'required|image|max:5120',
        ]);

        Photo::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'path' => $this->photo->store('photos'),
        ]);

        session()->flash('status', 'Photo uploaded successfully!');
        $this->reset();
        $this->dispatch('photo-uploaded');
    }

    public function render()
    {
        return view('livewire.photo-upload-modal');
    }
}
```

---

## Migration Workflow: Blank Canvas to Full App

### Phase 1: Foundation (Weeks 1–2) — ✅ Complete!

- ✓ Reset all page views to blank-canvas scaffolds
- ✓ Layout now supports Livewire
- ✓ All controller-referenced views exist
- **Next:** Create UI building blocks

### Phase 2: UI Building Blocks (Weeks 2–3)

1. **Create UI components** in `components/ui/`:
   - `button.blade.php` (variant, size, disabled states)
   - `card.blade.php` (title, flexible content)
   - `form-input.blade.php` (labels, validation errors)
   - `form-textarea.blade.php` (similar to form-input)
   - `modal.blade.php` (reusable modal container)
   - `badge.blade.php` (small UI elements)
   - `alert.blade.php` (success, warning, error messages)

2. **Test component composition:**
   - Verify `$attributes->merge()` works across components
   - Check Tailwind classes merge correctly

### Phase 3: Static Pages (Weeks 3–4)

1. **Rebuild static pages** (home, auth, about, contact) using Blade + UI components
2. **Test navigation flow** and ensure styling consistency
3. **Set up modal patterns** for common interactions (contact, confirmations)
4. **Add View Composers** for shared data (navigation, auth user)

### Phase 4: Feature Pages (Weeks 5–6)

1. **Rebuild feature pages** (photos, albums, posts) using components
2. **Use components for reusable UI** (cards, lists, forms)
3. **Ensure all routes work** without JavaScript dependencies

### Phase 5: Reactive Features (Weeks 7+)

1. **Livewire is already installed!** — Add components where interactivity is needed:
   - Photo gallery (filtering, search, real-time updates)
   - Form submissions (photo upload, album creation)
   - Comments and ratings (live refresh)

2. **Keep static Blade for read-only pages** (photo details, user profiles)

### Template Migration Checklist

- [ ] All page views have minimal scaffolds ✅
- [ ] Missing view files created ✅
- [ ] UI building blocks created (button, card, input, modal, etc.)
- [ ] Layout cleaned and Livewire wired ✅
- [ ] All routes resolve without error
- [ ] Test suite passes (`php artisan test`)
- [ ] Frontend builds cleanly (`bun run build`)
- [ ] First static page rebuilt (home or auth)
- [ ] First Livewire component created and tested
- [ ] Modal patterns integrated into a Livewire component

---

## Best Practices Summary

1. **Prefer Blade Components over `@include`** – Explicit props, cleaner data flow
2. **Use `$attributes->merge()` liberally** – Components become truly reusable
3. **Keep components small and focused** – One job per component
4. **Use View Composers for shared data** – Inject navigation, auth user, etc. automatically
5. **Start with static Blade, add Livewire incrementally** – React only where needed
6. **Test early and often** – Run `php artisan test` and `bun run build` after each phase
7. **Follow Laravel naming conventions:**
   - Views: **kebab-case** (`show-photos.blade.php`)
   - Components: **PascalCase in paths** → kebab-case in HTML (`<x-ui.form-input />`)
   - Livewire: **PascalCase** (`app/Livewire/ShowPhotos.php` → `<livewire:show-photos />`)

---

## Resources

- [Laravel Blade Documentation](https://laravel.com/docs/13.x/blade)
- [Blade Components Guide](https://laravel.com/docs/13.x/blade#components)
- [View Composers Documentation](https://laravel.com/docs/13.x/views#view-composers)
- [Livewire 4 Documentation](https://livewire.laravel.com)
- [Tailwind CSS Documentation](https://tailwindcss.com)

---

**Happy rebuilding!** Start with the building-block components, use the blank-canvas pages as your foundation, and progressively adopt Livewire as you gain confidence. 🎨
