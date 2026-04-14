{{-- 
  Lucide SVG Icon Component
  Usage: <x-icon name="camera" class="w-5 h-5" />
  
  Common icons:
  - camera, image, images, folder, lock, unlock, globe, star, search, 
  - thumbs-up, target, pen-tool, grid, list, trash, plus, edit, eye,
  - chevron-down, menu, x, check, alert
--}}

@props([
    'name' => 'help-circle',
    'class' => 'w-5 h-5',
])

<svg 
    {{ $attributes->merge(['class' => $class]) }}
    xmlns="http://www.w3.org/2000/svg" 
    width="24" 
    height="24" 
    viewBox="0 0 24 24" 
    fill="none" 
    stroke="currentColor" 
    stroke-width="2" 
    stroke-linecap="round" 
    stroke-linejoin="round"
    aria-hidden="true"
>
    @switch($name)
        {{-- Media/Photos --}}
        @case('camera')
            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"></path>
            <circle cx="12" cy="13" r="3"></circle>
            @break

        @case('image')
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <circle cx="8.5" cy="8.5" r="1.5"></circle>
            <polyline points="21 15 16 10 5 21"></polyline>
            @break

        @case('images')
            <path d="M18 20H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14l4 4v10a2 2 0 0 1-2 2z"></path>
            <polyline points="15 9 11 5"></polyline>
            @break

        @case('folder')
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
            @break

        {{-- Privacy/Security --}}
        @case('lock')
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            @break

        @case('unlock')
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
            @break

        @case('globe')
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            @break

        {{-- Ratings/Favorites --}}
        @case('star')
            <polygon points="12 2 15.09 10.26 24 10.27 17.18 16.25 20.09 24.5 12 18.5 3.91 24.5 6.82 16.25 0 10.27 8.91 10.26 12 2"></polygon>
            @break

        @case('heart')
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            @break

        {{-- Search/Navigation --}}
        @case('search')
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
            @break

        @case('sliders')
            <line x1="4" y1="6" x2="4" y2="18"></line>
            <line x1="12" y1="8" x2="12" y2="16"></line>
            <line x1="20" y1="5" x2="20" y2="19"></line>
            <polyline points="1 6 7 6"></polyline>
            <polyline points="9 8 15 8"></polyline>
            <polyline points="17 5 23 5"></polyline>
            <polyline points="1 18 7 18"></polyline>
            <polyline points="9 16 15 16"></polyline>
            <polyline points="17 19 23 19"></polyline>
            @break

        @case('grid')
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
            @break

        @case('list')
            <line x1="8" y1="6" x2="21" y2="6"></line>
            <line x1="8" y1="12" x2="21" y2="12"></line>
            <line x1="8" y1="18" x2="21" y2="18"></line>
            <line x1="3" y1="6" x2="3.01" y2="6"></line>
            <line x1="3" y1="12" x2="3.01" y2="12"></line>
            <line x1="3" y1="18" x2="3.01" y2="18"></line>
            @break

        {{-- Actions --}}
        @case('plus')
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
            @break

        @case('trash')
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
            @break

        @case('edit')
            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
            @break

        @case('eye')
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
            @break

        @case('eye-off')
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
            @break

        @case('thumbs-up')
            <path d="M14 9V5a2 2 0 0 0-2-2H6l-.9 2.263a1 1 0 0 0 .109 1.092l1.573 2.177A1 1 0 0 1 7 9v6a2 2 0 0 0 2 2h5.5a3.5 3.5 0 0 0 3.447-2.802l1.482-7.41a2 2 0 0 0-1.964-2.488H14.5"></path>
            <path d="M5 9H3a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"></path>
            @break

        {{-- UI/Navigation --}}
        @case('menu')
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
            @break

        @case('x')
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
            @break

        @case('chevron-down')
            <polyline points="6 9 12 15 18 9"></polyline>
            @break

        @case('chevron-up')
            <polyline points="18 15 12 9 6 15"></polyline>
            @break

        @case('chevron-left')
            <polyline points="15 18 9 12 15 6"></polyline>
            @break

        @case('chevron-right')
            <polyline points="9 18 15 12 9 6"></polyline>
            @break

        @case('arrow-left')
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
            @break

        @case('arrow-right')
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
            @break

        {{-- Status/Feedback --}}
        @case('check')
            <polyline points="20 6 9 17 4 12"></polyline>
            @break

        @case('alert')
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
            @break

        @case('alert-circle')
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
            @break

        {{-- Content/Writing --}}
        @case('pen-tool')
            <path d="M3 17.6915026L17.6915026 3M20.7243136 9.86l-3.6 -3.6"></path>
            @break

        @case('pen')
            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
            @break

        {{-- Markdown/Text Formatting --}}
        @case('bold')
            <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
            <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
            @break

        @case('italic')
            <line x1="19" y1="4" x2="10" y2="4"></line>
            <line x1="14" y1="20" x2="5" y2="20"></line>
            <line x1="15" y1="4" x2="9" y2="20"></line>
            @break

        @case('heading')
            <path d="M6 4v16M18 4v16M4 10h16"></path>
            @break

        @case('quote')
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            @break

        @case('list')
            <line x1="8" y1="6" x2="21" y2="6"></line>
            <line x1="8" y1="12" x2="21" y2="12"></line>
            <line x1="8" y1="18" x2="21" y2="18"></line>
            <line x1="3" y1="6" x2="3.01" y2="6"></line>
            <line x1="3" y1="12" x2="3.01" y2="12"></line>
            <line x1="3" y1="18" x2="3.01" y2="18"></line>
            @break

        @case('link-2')
            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
            @break

        @case('code')
            <polyline points="16 18 22 12 16 6"></polyline>
            <polyline points="8 6 2 12 8 18"></polyline>
            @break

        @case('target')
            <circle cx="12" cy="12" r="1"></circle>
            <circle cx="12" cy="12" r="5"></circle>
            <circle cx="12" cy="12" r="9"></circle>
            @break

        {{-- Social Media --}}
        @case('linkedin')
            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"></path>
            <circle cx="4" cy="4" r="2"></circle>
            @break

        @case('github')
            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v 3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"></path>
            @break

        @case('orcid')
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.5 8.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>
            <path d="M14.5 8.5h2a1.5 1.5 0 0 1 0 3h-2M14.5 8.5h-1"></path>
            @break

        @case('book-open')
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zM22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            @break

        @case('phone')
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            @break

        {{-- Default fallback --}}
        @default
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 8v4M12 16h.01"></path>
    @endswitch
</svg>
