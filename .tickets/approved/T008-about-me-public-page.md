<instructions>
DOCUMENT TYPE: Proposal Ticket (T008-about-me-public-page.md)

This proposal intentionally combines original T008 (About Me public page) and proposed T009 (Contact modal) so both can be implemented in one pass.

STATUS: Proposal approved for implementation.
</instructions>

# T008: About Me Public Page + Contact Modal

**Status:** APPROVED  
**Tag:** `about-me-public-page`

---

## Problem Statement

The application has CV/profile data in the user schema but no public About Me page, and the related Contact Modal (T009) is split from the same surface even though both features should ship together.

## Specifications

### Feature Requirements
1. **Public About Me route** — Add a public profile page at `/users/{user}` that displays user CV/profile data from T007.
2. **CV content rendering** — Show professional summary, academic history, professional experience (reverse chronological), skills/qualifications, and optional links/certifications when present.
3. **Profile presentation** — Display the user name, email, and profile photo if available; use clear empty states when optional CV sections are missing.
4. **Integrated Contact Modal (T009 scope)** — Add a "Contact Me" button on the About Me page that opens an HTML dialog/modal with contact details.
5. **Contact visibility rules** — In the modal, always show name/email/LinkedIn when available; only show phone when `phone_public = true`.
6. **Accessibility and UX** — Modal must support keyboard access (open, close, focus handling, ESC close) and maintain brand-kit styling consistency.

### Technical Requirements
- **Database:** No new migrations. Reuse existing `users` CV columns introduced by T007 (`bio`, `phone`, `phone_public`, `linkedin`, `academic_history`, `professional_experience`, `skills`, `certifications`, `orcid_id`, `github`, `other_links`).
- **Routes:** Add public route `GET /users/{user}` named `users.show` with numeric route constraint to avoid path collisions.
- **Dependencies:** No new packages. Reuse existing Blade/Tailwind stack and existing markdown rendering approach already used in the project.
- **Files:**
  - `routes/web.php` (add public About Me route)
  - `app/Http/Controllers/UserController.php` (add `show` action and profile data shaping)
  - `resources/views/users/show.blade.php` (public About Me page)
  - `resources/views/users/partials/contact-modal.blade.php` (contact modal partial)
  - `resources/views/layouts/partials/header.blade.php` (optional link entry point)
  - `tests/Feature/UserPublicProfileTest.php` (coverage for page render and modal visibility behavior)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **add-public-profile-route** — Add `users.show` public route with proper parameter constraint and route naming for stable linking.
2. **implement-user-show-action** — Implement controller action that loads/normalizes CV arrays and sorts experience history for deterministic rendering (depends: add-public-profile-route).
3. **build-about-me-page** — Create `users/show.blade.php` with CV sections, profile header, and consistent empty states using brand-kit classes (depends: implement-user-show-action).
4. **integrate-contact-modal** — Implement contact modal partial and About Me button wiring (native dialog behavior + JS hooks) so T009 is fully delivered inside T008 (depends: build-about-me-page).
5. **enforce-contact-visibility-rules** — Ensure phone visibility respects `phone_public` and contact fields only render when data exists (depends: integrate-contact-modal).
6. **add-feature-tests** — Add feature tests for public route accessibility, CV section rendering, and modal contact field visibility rules (depends: enforce-contact-visibility-rules).

---

## Resolution Summary

Not started. This proposal defines combined delivery for About Me page and Contact Modal.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** pending implementation
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
