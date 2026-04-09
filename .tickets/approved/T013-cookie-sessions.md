# T013: Cookie-Based Sessions

**Status:** APPROVED  
**Tag:** `cookie-sessions`

---

## Problem Statement

Theme preference and session data are not persisted across browser sessions via cookies, requiring users to reconfigure preferences on each visit.

## Specifications

### Feature Requirements
1. **Cookie-based theme persistence** — Store theme preference ('dark' or 'light') in a long-lived cookie (1 year expiration)
2. **Session cookie configuration** — Ensure Laravel session driver uses cookies with secure, httpOnly, and sameSite settings
3. **Theme restoration** — On page load, read theme cookie and apply dark/light class to HTML element before render
4. **ThemeController updates** — Refactor ThemeController to set cookie alongside session storage
5. **Backward compatibility** — Maintain existing session-based theme toggle behavior while adding cookie layer
6. **Security compliance** — Follow Laravel 13 cookie security best practices (encrypted, httpOnly, secure in production)

### Technical Requirements
- **Database:** No migrations required. Cookie storage is client-side with server-side encryption.
- **Routes:** Reuse existing theme.toggle route; no new routes needed.
- **Dependencies:** No new packages. Use Laravel's built-in Cookie facade and session config.
- **Files:**
  - `app/Http/Controllers/ThemeController.php` (add cookie setters with 1-year expiration)
  - `config/session.php` (verify cookie driver and security settings)
  - `resources/views/layouts/app.blade.php` (read theme cookie on load for instant class application)
  - `resources/js/theme.js` (update localStorage logic to sync with cookie if needed)
  - `tests/Feature/ThemePersistenceTest.php` (feature tests for cookie creation and theme restoration)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **update-theme-controller** — Modify ThemeController::toggle to set a 'theme' cookie with 1-year expiration alongside session storage using Cookie::queue()

2. **verify-session-config** — Check config/session.php for 'cookie' driver or ensure cookies are enabled; set secure, httpOnly, sameSite attributes appropriately (depends: update-theme-controller)

3. **implement-theme-restoration** — In app.blade.php, read theme cookie value and apply 'dark' or 'light' class to <html> element server-side or via inline script before DOM renders (depends: update-theme-controller)

4. **sync-client-storage** — Ensure theme.js reads and syncs theme preference from cookie to localStorage for consistency across storage mechanisms (depends: implement-theme-restoration)

5. **test-cookie-persistence** — Write feature tests to verify theme cookie is set on toggle, survives session restart, and correctly restores theme on next visit (depends: sync-client-storage)

6. **verify-security-compliance** — Confirm cookies are encrypted, httpOnly, secure (in production), and follow Laravel 13 best practices per framework docs (depends: test-cookie-persistence)

---

## Resolution Summary

Not started. Proposal defines cookie-based theme persistence for cross-session continuity.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** feature tests for cookie behavior and theme restoration
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
