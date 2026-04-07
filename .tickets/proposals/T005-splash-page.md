# T005: Animated Splash Page

**Status:** IN PROGRESS  
**Tag:** `splash-page`

---

## Problem Statement

The application lacks a branded entry experience with the VNT logo animation as specified in the brand kit, resulting in no first-visit impression or brand reinforcement.

## Specifications

### Feature Requirements
1. **First-visit detection** — Display splash modal only on user's first visit, tracked via localStorage or session flag
2. **Modal presentation** — Show splash as a centered modal overlay (not fullscreen redirect) over the homepage/dashboard
3. **VNT logo display** — Render the VNT logo lockup using brand-kit CSS classes (.vnt-logo, .vnt-wordmark, .vnt-sub) at proper size (minimum 3rem wordmark)
4. **Subtle animation** — Apply fade-in animation to logo elements (no elaborate motion per brand kit: "no animation on the logo. Ever." means structural animation, not entry fade)
5. **Dismissal** — Auto-dismiss after 2-3 seconds OR provide a close button for manual dismissal
6. **Brand compliance** — Follow brand kit strictly: zinc palette, 2px radius, no shadows, Inter font, institutional voice

### Technical Requirements
- **Database:** No new migrations. Use localStorage for first-visit tracking (client-side only).
- **Routes:** No new routes. Enhance homepage/dashboard with modal conditional rendering.
- **Dependencies:** No new packages. Pure CSS animations and vanilla JavaScript.
- **Files:**
  - `resources/views/components/splash-modal.blade.php` (splash modal component)
  - `resources/js/splash.js` (first-visit detection, auto-dismiss, localStorage handling)
  - `resources/css/app.css` (splash modal styles, fade-in animations, brand-kit compliance)
  - `resources/views/dashboard.blade.php` (include splash modal component)
  - `resources/js/app.js` (import splash module)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **create-splash-modal-component** — Build Blade component with VNT logo lockup, modal container, backdrop, and optional close button using brand-kit CSS classes

2. **implement-first-visit-detection** — Write JavaScript module to check localStorage for 'splash_shown' flag; show modal if absent, set flag after display (depends: create-splash-modal-component)

3. **add-animation-styles** — Apply CSS fade-in animation to logo elements and modal backdrop, respecting brand kit constraints (no elaborate motion) (depends: create-splash-modal-component)

4. **implement-auto-dismiss** — Add setTimeout logic to auto-close splash after 2.5 seconds, with option for manual close button click (depends: implement-first-visit-detection)

5. **integrate-into-homepage** — Include splash modal component in dashboard.blade.php with conditional rendering only when user is new visitor (depends: add-animation-styles)

6. **test-brand-compliance** — Verify zinc palette, 2px radius, Inter font, no shadows, and logo sizing (wordmark ≥ 3rem) (depends: integrate-into-homepage)

---

## Resolution Summary

Not started. Proposal defines first-visit splash modal with VNT logo and brand-compliant presentation.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** manual testing for localStorage behavior and animation smoothness
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
