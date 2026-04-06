# Project Specifications

## Agent instructions

Use this documenent as the guidelines for the expectations of the software. The specification adapts to the schema.

> [!important]
> This app is not meant for production. It is a school project that only demands a tech demo and proof of concept. Data will mostly be populated with seeders to simulate a live setup. Real users inputs and interactions will still need to be handled by the database.

> [!task]
> Analyze the document and compare with the codebase. Flag the completed implementations and write them as resolved in .tickets/resolved/, make proposed tickets for features in .tickets/proposals that have not been implemented.  TODO's are explicit issues that need to be opened. You are allowed to make tickets when there is no TODO provided it makes sense to catch. Include the proposed solutions. Treat the TODOs as annotations. They are proposals to be reviewed by the user. Ensure you are using the Laravel Boost MCP server and using up to date Laravel 13 Standards.

## User Specifications

```markdown
# VANITI FAIRE — Brand Kit

# A Product of VNT GmbH

-----

## Agent Instructions for the frontend design

This document is the sole design authority for VANITI FAIRE.
Apply every rule here strictly and without interpretation.
Do not introduce any color, typeface, radius, shadow, animation,
icon library, or spacing convention not explicitly defined here.
Do not override rules based on aesthetic judgment.
Do not suggest improvements to the brand direction.
When in doubt, do less.

Stack: Laravel 13, Blade, Tailwind CSS, Lucide icons.
No component library. No shadcn. No Bootstrap. No Alpine unless
explicitly introduced later.
All UI is pure Blade templates with Tailwind utility classes.
CSS variables are declared in resources/css/app.css only.
Tailwind config extensions are declared in tailwind.config.js only.

-----

## Identity

Product name:     VANITI FAIRE
Parent company:   VNT GmbH
Tagline:          None. Never add one.
Domain language:  The product does not explain itself.

-----

## Logo Lockup

Render in CSS only. No SVG. No image. No canvas.

Structure:

```html
<div class="vnt-logo">
  <span class="vnt-wordmark">VANITI FAIRE</span>
  <span class="vnt-sub">VNT GmbH</span>
</div>
```

CSS:

```css
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
  /* letter-spacing must be tuned manually per render size
     until .vnt-sub width === .vnt-wordmark width exactly.
     Use letter-spacing: Xem and adjust in increments of 0.01em. */
  letter-spacing: 0.43em;
}
```

Rules:

- Appears on splash page and footer only. Nowhere else.
- Minimum size: vnt-wordmark at 3rem. Never smaller.
- No animation on the logo. Ever.
- No color variation. Foreground on background only.
- No tagline beneath or beside it.
- No enclosing box, border, or background treatment.

-----

## Typography

Typeface: Inter
Weights: 400 (Regular), 700 (Bold)
Source: Self-hosted. No Google Fonts CDN. No Bunny CDN.
Obtain via google-webfonts-helper.vercel.app
Place in: public/fonts/inter/
Declare in: resources/css/app.css

```css
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
```

Scale:

|Role      |Weight |Size    |Class                |
|----------|-------|--------|---------------------|
|Display   |Bold   |2.25rem |text-4xl font-bold   |
|Heading 1 |Bold   |1.875rem|text-3xl font-bold   |
|Heading 2 |Bold   |1.5rem  |text-2xl font-bold   |
|Heading 3 |Bold   |1.25rem |text-xl font-bold    |
|Body      |Regular|1rem    |text-base font-normal|
|Small     |Regular|0.875rem|text-sm font-normal  |
|Label     |Bold   |0.875rem|text-sm font-bold    |
|Fine print|Regular|0.75rem |text-xs font-normal  |
|Mono      |Regular|0.875rem|text-sm font-mono    |

Rules:

- Inter everywhere. No exceptions.
- Never italic. Ever.
- Never mix weights mid-sentence.
- UI copy is sentence case.
- Logo is all caps. Nothing else is all caps.
- Mono is for code output only. Never for UI labels or navigation.
- No text decoration except underline on interactive links,
  applied via hover state only.

-----

## Color System

Strategy: Zinc scale only. No brand accent color.
The only non-zinc color permitted is destructive red.
No gradients. No opacity tricks for color variation.
Use border and background contrast for separation.

Declare in resources/css/app.css:

```css
:root {
  --color-background:         #FAFAFA;
  --color-foreground:         #09090B;
  --color-card:               #FFFFFF;
  --color-card-foreground:    #09090B;
  --color-border:             #E4E4E7;
  --color-input:              #E4E4E7;
  --color-primary:            #18181B;
  --color-primary-foreground: #FAFAFA;
  --color-secondary:          #F4F4F5;
  --color-secondary-foreground: #18181B;
  --color-muted:              #F4F4F5;
  --color-muted-foreground:   #71717A;
  --color-destructive:        #DC2626;
  --color-ring:               #09090B;
}

.dark {
  --color-background:         #09090B;
  --color-foreground:         #FAFAFA;
  --color-card:               #18181B;
  --color-card-foreground:    #FAFAFA;
  --color-border:             #27272A;
  --color-input:              #27272A;
  --color-primary:            #FAFAFA;
  --color-primary-foreground: #18181B;
  --color-secondary:          #27272A;
  --color-secondary-foreground: #FAFAFA;
  --color-muted:              #27272A;
  --color-muted-foreground:   #A1A1AA;
  --color-destructive:        #EF4444;
  --color-ring:               #D4D4D8;
}
```

Dark mode strategy:

- class=“dark” on the <html> element
- toggled via Laravel session or JS, persisted in cookie
- default is dark mode

Tailwind config — extend with CSS variables:

```js
// tailwind.config.js
theme: {
  extend: {
    colors: {
      background:   'var(--color-background)',
      foreground:   'var(--color-foreground)',
      card:         'var(--color-card)',
      border:       'var(--color-border)',
      input:        'var(--color-input)',
      primary: {
        DEFAULT:    'var(--color-primary)',
        foreground: 'var(--color-primary-foreground)',
      },
      secondary: {
        DEFAULT:    'var(--color-secondary)',
        foreground: 'var(--color-secondary-foreground)',
      },
      muted: {
        DEFAULT:    'var(--color-muted)',
        foreground: 'var(--color-muted-foreground)',
      },
      destructive:  'var(--color-destructive)',
    },
  },
},
```

-----

## Spacing and Layout

Scale: Tailwind default. Do not add custom spacing values.
Density: tight. Default to less padding, not more.
Maximum content width: max-w-5xl, centered.
No full-bleed content sections except the splash page.

Grid:

- Use Tailwind grid utilities only.
- No CSS grid written by hand unless Tailwind cannot express it.
- No masonry layouts. No asymmetric editorial layouts.
- Everything is on-grid. Nothing floats decoratively.

-----

## Shape

Border radius: 2px universally.

```css
/* in app.css */
--radius: 2px;
```

```js
// tailwind.config.js
borderRadius: {
  DEFAULT: 'var(--radius)',
  none: '0',
  sm: 'var(--radius)',
  md: 'var(--radius)',
  lg: 'var(--radius)',
  full: '9999px', /* permitted only for avatar images */
},
```

Rules:

- 2px on all interactive elements: buttons, inputs, cards, badges.
- rounded-full permitted only for user avatar images.
- Nothing softer than 2px. No pill buttons. No rounded-lg.

-----

## Elevation

No box shadows. Anywhere. Ever.
Separation is achieved through:

- border: 1px solid var(–color-border)
- background contrast between card and background
- negative space

If the agent is tempted to add a shadow, use a border instead.

-----

## Iconography

Library: Lucide
Install: bun install lucide (already available if using
Laravel’s default Vite + bun setup)
Import per icon in JS/Blade as needed.

Style rules:

- Outline only. Never filled.
- stroke-width: 1.5 (Lucide default, do not change)
- Size: 16px default, 20px for emphasis
- Never below 14px
- Color: currentColor always. Never hardcoded.
- No icon-only buttons without a tooltip or aria-label.

Do not install Font Awesome, Heroicons, or any other icon library.

-----

## Motion

Permitted animations:

1. Splash page entrance — defined separately in splash spec.
1. Functional transitions only:
- Duration: 150ms
- Easing: ease
- Properties: opacity, transform (translate only)

Prohibited:

- No bounce
- No spring physics
- No decorative motion
- No scroll-triggered animations outside splash
- No hover animations except opacity and border-color transitions

Tailwind transition classes to use:
transition-opacity, transition-colors, transition-transform
duration-150 ease-in

-----

## Component Patterns

### Buttons

```html
<!-- Primary -->
<button class="bg-primary text-primary-foreground font-bold
               text-sm px-4 py-2 rounded border border-primary
               hover:opacity-90 transition-opacity duration-150">
  Label
</button>

<!-- Secondary -->
<button class="bg-secondary text-secondary-foreground font-bold
               text-sm px-4 py-2 rounded border border-border
               hover:opacity-90 transition-opacity duration-150">
  Label
</button>

<!-- Destructive -->
<button class="bg-destructive text-white font-bold
               text-sm px-4 py-2 rounded
               hover:opacity-90 transition-opacity duration-150">
  Label
</button>

<!-- Ghost -->
<button class="bg-transparent text-foreground font-bold
               text-sm px-4 py-2 rounded
               hover:bg-secondary transition-colors duration-150">
  Label
</button>
```

### Inputs

```html
<input type="text"
       class="w-full bg-background text-foreground text-sm
              border border-input rounded px-3 py-2
              focus:outline-none focus:ring-1 focus:ring-ring
              placeholder:text-muted-foreground" />
```

### Cards

```html
<div class="bg-card text-card-foreground border border-border
            rounded p-4">
  <!-- content -->
</div>
```

### Badges

```html
<!-- Default -->
<span class="bg-secondary text-secondary-foreground font-bold
             text-xs px-2 py-0.5 rounded">
  Label
</span>

<!-- Experimental — required for all experimental features -->
<span class="bg-secondary text-muted-foreground font-bold
             text-xs px-2 py-0.5 rounded tracking-wide">
  EXPERIMENTAL
</span>
```

-----

## Experimental Feature Treatment

Any feature marked experimental in the spec must include:

1. EXPERIMENTAL badge adjacent to the feature heading
1. Disclaimer block:

```html
<div class="border border-border rounded p-3 text-xs
            text-muted-foreground space-y-1">
  <p class="font-bold text-foreground">Notice</p>
  <p>
    This feature is unstable and may produce unexpected results.
    VNT GmbH assumes no liability for outputs generated through
    experimental functionality.
  </p>
</div>
```

-----

## Voice and Copy

Tone: institutional, declarative, cold.
Register: formal. Never casual. Never warm.

Prohibited words and constructions:

- welcome, hello, hi, hey
- let’s, we’ll, you’ll love
- amazing, powerful, seamless, intuitive
- exclamation marks anywhere in UI copy
- second person warmth (“your journey”, “your story”)

Preferred constructions:

- Passive voice where appropriate
- Declarative statements
- Bureaucratic precision

Examples:

|Avoid                       |Use instead              |
|----------------------------|-------------------------|
|Welcome back!               |Session established.     |
|You’re all set.             |Registration complete.   |
|Upload your photo           |Submit image file.       |
|Share your memories         |Post to record.          |
|Something went wrong        |An error was encountered.|
|You don’t have any posts yet|No records found.        |

Error messages: factual, no apology, no emoji.
Success messages: confirmatory, no celebration.

-----

## Blade Layout Convention

Master layout: resources/views/layouts/app.blade.php
Dark mode class on html element, read from session or cookie.

```html
<!DOCTYPE html>
<html lang="en" class="{{ session('theme', 'dark') }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>VANITI FAIRE — @yield('title')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground font-sans antialiased
             min-h-screen">

  @include('layouts.partials.header')

  <main class="max-w-5xl mx-auto px-4 py-8">
    @yield('content')
  </main>

  @include('layouts.partials.footer')

</body>
</html>
```

Footer must include the VNT logo lockup.
Header must not include the VNT logo lockup.
Header contains navigation only.

-----

## File Structure Reference

```
resources/
  css/
    app.css          ← CSS variables, @font-face, base resets
  views/
    layouts/
      app.blade.php  ← master layout
      partials/
        header.blade.php
        footer.blade.php
  js/
    app.js           ← Lucide icon init, theme toggle

public/
  fonts/
    inter/
      inter-400.woff2
      inter-700.woff2

tailwind.config.js   ← color extensions, radius overrides
```
```

## What This Brand Kit Does Not Cover

The following are defined in separate spec documents:

- Splash page animation and entrance sequence
- Navigation structure and routing
- Page-level layouts beyond the master template
- AI image generator UI (experimental feature spec)
- Admin dashboard UI
- Guestbook page UI
- Photo album grid and slide mode

Do not make design decisions for the above based on inference
from this document. Wait for the relevant spec or ticket.
```
```

## Expected Target

> TODO: AI Generated pictures will be provided by user in the future as seed data. Have a system for handling them. Defer with placeholders for now.
> TODO: An upload system must include image cropping, everything happens on the frontend and the backend only receives the cropped image--use bun's cropper.js.
> TODO: Image normalizer should convert any uploads to webp format as well as strip the original metadata. Moreover, it should compress images when needed to mitigate high resolution and large high quality uploads.

> [!important]
> Everything below in here assumes PHP + MySQL CRUD app. But we are mogging the curriculum so we are building in Laravel 13.

1. Session validation
2. Uses master templates (header, body, and footer) 
3. Uses an animated splash page, dynamic pages, and video integration.
4. password with hashing
5. +4 baby AI-generated picture 1 to 12 months 
6. +6 highlights/milestones from Grade 1 to Grade 6 with descriptions, using AI picture generated or captured moments
7. +8 highlights/milestones from 1st year HS to College
8. Registered users can have a picture rating 1 star to 5 stars
9. posted comments for each picture
10. The system will have a graphical analysis with
	1. highest to lowest picture rating
	2. highest to lowest picture rating posted comments    
11. Users can upload a profile picture or an avatar
12. A guestbook page (All Users)
13. All users' activity will include their profile picture or avatar.

TODO: Innovate by adding an admin dashboard that tracks number of online concurrent users, users over time/website traffic, 

TODO: Innovate by adding metrics for user posts that track engagements over time (use what is available in the schema, do not add anything more). Use the user timestamps and session timestamps

TODO: Innovate by adding an AI image generator that can accept any API key from Google AI studios/Gemini API key. It must hanlle requests that can last up to 30 seconds to 1 minute to account for generation and latency. It must have presets. Make prompts for little boy, little girl, groom, bride, man selfie in big ben, woman selfie in big bem. It must also accept custom prompts. Make sure the feature in the UI includes "experimental" tag and has a disclaimer for being unstable.

TODO: use cookies for keeping sessions.

## Types of Users

Guest User: 
Non-authenticated users visiting the system or application without logging in. They have limited, read-only permissions. Their activity may be tracked through temporary session storage, such as cookies, which can be used to convert them into a registered user. 

Admin (Administrator):
Users with the highest privileges are responsible for configuration, security, and managing other users. They have full access to system settings, application screens, and data operations.

Registered (Authenticated) User: 
Users who have created an account and logged in. They are recognized by the system and can access specific data, perform actions, and maintain persistent data across sessions. This includes roles like customers, members, or developers.

## Functional Guidelines

At a minimum, your application should allow users to perform these actions:
- User Management: Registration and login systems using PHP Sessions to ensure users can only view and manage their own photos.    
- Album Organization: Ability to create multiple distinct albums, each with a specific title and cover image.
- Photo Management:
	- Multi-image upload capabilities.
	- Automated generation of unique directories for each user's images.
	- Ability to add titles and comments to individual photos.
- Viewing Options: Toggle between thumbnail grids and a full-screen "slide mode" for viewing pictures.
- Features
	- Guestbook page TODO: guestbook should act like the global feed
	- About Me page TODO: update the user schema to allow for this
	- Contact Me page TODO: could just be a modal you can access with a button in About Me
	- Photo Album page(s)

## About Me
An "About Me" page with a Curriculum Vitae (CV) is a personal website section or document that provides a detailed overview of professional, academic, and research experience, ideal for academia or specialized industries. It highlights career history, key accomplishments, publications, and skills, offering more depth than a one-page resume.
Key Components to Include (with picture(s) if there are any, not AI-generated):
- Professional Summary: A brief, compelling introduction highlighting your expertise and goals.
- Contact Information: Name, phone number, email, and LinkedIn profile.
- Academic History: Degrees, institution names, and graduation dates.
- Professional Experience (if there are any): Detailed work history in reverse chronological order, emphasizing accomplishments.
- Skills & Qualifications: Key technical and soft skills relevant to the field.
- Optional Specialized Sections: Publications, presentations, grants, awards, certifications, and professional associations.
