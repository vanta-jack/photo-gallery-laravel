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
# Product of VNT GmbH

## Agent Instructions
Apply this brand kit strictly. Do not introduce colors, typefaces, 
radii, or icon libraries outside this specification. shadcn 
components should be themed via CSS variables only. Do not override 
shadcn classes directly. Use Laravel Boost MCP for Blade integration 
and shadcn MCP for component generation. Dark mode is a requirement 
not an option.

---

## Typography

Font: Inter (self-hosted, no CDN)
Weights used: 400, 700
Source: bunny.net/fonts or google-webfonts-helper for self-hosting

### Logo Lockup
- Line 1: VANITI FAIRE — Inter Bold, all caps, tracking-normal
- Line 2: VNT GmbH — Inter Bold, font-size ~30% of line 1,
  letter-spacing adjusted so text width === line 1 width exactly
- Wrapper: inline-block div, no decoration, no animation
- Render in CSS only, no SVG, no image

### Type Scale
Display:  Inter Bold    — headers, page titles
Body:     Inter Regular — prose, descriptions  
Label:    Inter Bold    — UI labels, buttons, nav
Mono:     System mono   — only for code, never for UI

### Rules
- Never mix weights mid-sentence
- Never use italic
- UI copy is sentence case except logo which is all caps
- No decorative type anywhere

---

## Color Palette
Base: shadcn zinc theme — do not deviate

### Light Mode
--background:   #FAFAFA   /* zinc-50  */
--foreground:   #09090B   /* zinc-950 */
--card:         #FFFFFF
--card-foreground: #09090B
--border:       #E4E4E7   /* zinc-200 */
--input:        #E4E4E7
--primary:      #18181B   /* zinc-900 */
--primary-foreground: #FAFAFA
--secondary:    #F4F4F5   /* zinc-100 */
--secondary-foreground: #18181B
--muted:        #F4F4F5
--muted-foreground: #71717A /* zinc-500 */
--accent:       #F4F4F5
--accent-foreground: #18181B
--destructive:  #DC2626
--ring:         #09090B

### Dark Mode
--background:   #09090B   /* zinc-950 */
--foreground:   #FAFAFA   /* zinc-50  */
--card:         #18181B   /* zinc-900 */
--card-foreground: #FAFAFA
--border:       #27272A   /* zinc-800 */
--input:        #27272A
--primary:      #FAFAFA
--primary-foreground: #18181B
--secondary:    #27272A
--secondary-foreground: #FAFAFA
--muted:        #27272A
--muted-foreground: #A1A1AA /* zinc-400 */
--accent:       #27272A
--accent-foreground: #FAFAFA
--destructive:  #EF4444
--ring:         #D4D4D8

### Rules
- No brand accent color. Zinc only.
- No gradients anywhere
- No color used for decoration, only for function
- Destructive red is the only non-zinc color permitted

---

## Iconography
Library: Lucide (already bundled with shadcn)
Style: outline only, never filled
Size: 16px default, 20px for emphasis, never below 14px
Color: inherits currentColor always
Do not install Font Awesome or Heroicons

---

## Shape and Spacing

Border radius:
--radius: 2px
Apply 2px universally. Nothing softer. 
Buttons, cards, inputs — all 2px.

Spacing scale: Tailwind default
Density: tight. Prefer less padding over more.
No decorative dividers. Use border only when structurally necessary.

---

## Elevation and Shadow
No box shadows in light or dark mode.
Separation is achieved through border and background contrast only.

---

## Motion
No animation except:
- Splash page entrance (single, defined elsewhere)
- Functional transitions: 150ms ease, opacity or translate only
No bounce, no spring, no decorative motion

---

## Component Behavior (shadcn)
- Generate all components via shadcn MCP
- Apply CSS variable overrides in app.css only
- Dark mode via class strategy: class="dark" on html element
- Laravel Blade components wrap shadcn output
- Do not use shadcn's default blue accent anywhere

---

## Voice and Copy
Tone: institutional, declarative, never warm
Avoid: "welcome", "hello", "let's", exclamation marks
Use: passive constructions, bureaucratic precision
Example: "Your session has been established." 
Not: "You're logged in!"

Experimental features must include:
- Badge: EXPERIMENTAL
- Disclaimer: "This feature is unstable and may produce 
  unexpected results. VNT GmbH assumes no liability."

---

## Logo Usage Rules
- Appears on splash and footer only
- No color variation, no sizing below 48px for VANITI FAIRE line
- No tagline ever
- No animation on the logo itself
- Light mode: foreground zinc-950 on zinc-50
- Dark mode: foreground zinc-50 on zinc-950
```

## Expected Target

> TODO: AI Generated pictures will be provided by user in the future as seed data. Have a system for handling them. Defer with placeholders for now.
> TODO: An upload system must include image cropping, everything happens on the frontend and the backend only receives the cropped image.
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
