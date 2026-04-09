# T006: Guestbook as Global Feed

**Status:** RESOLVED  
**Tag:** `guestbook-global-feed`

---

## Problem Statement

The current guestbook displays entries in a simple list format without aggregating posts and photos as a unified feed with user avatars, limiting social engagement and content discovery.

## Specifications

### Feature Requirements
1. **Unified feed display** — Refactor guestbook to show aggregated posts and photos in a single chronological feed (most recent first)
2. **User avatar display** — Show profile photo (or fallback placeholder) next to each feed item with user's name
3. **Content type indicators** — Clearly differentiate between photo posts and text posts with visual cues
4. **Engagement preview** — Display vote/rating counts and comment counts for each feed item
5. **Navigation to detail** — Each feed item links to full photo or post detail page
6. **Brand-compliant design** — Follow VANITI FAIRE brand kit: zinc palette, 2px radius, no shadows, Inter font

### Technical Requirements
- **Database:** No new migrations. Use existing guestbook_entries junction table with post_id and photo_id relationships.
- **Routes:** Reuse existing guestbook.index route; enhance controller logic to aggregate posts/photos.
- **Dependencies:** No new packages. Use existing Eloquent relationships and Blade components.
- **Files:**
  - `app/Http/Controllers/GuestbookEntryController.php` (refactor index method to aggregate posts/photos)
  - `resources/views/guestbook/index.blade.php` (redesign as unified feed with user avatars)
  - `resources/views/guestbook/partials/feed-item.blade.php` (reusable feed item component)
  - `resources/views/guestbook/partials/user-avatar.blade.php` (avatar component with fallback)
  - `tests/Feature/GuestbookFeedTest.php` (feature tests for feed aggregation and rendering)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **refactor-controller-aggregation** — Update GuestbookEntryController::index to eager-load posts and photos with users/engagement data, sort by created_at DESC

2. **create-avatar-component** — Build reusable Blade component for user avatar display with profile photo or fallback initial/placeholder (depends: refactor-controller-aggregation)

3. **create-feed-item-component** — Build feed item partial that renders post or photo content with avatar, metadata, engagement counts, and link to detail (depends: create-avatar-component)

4. **redesign-guestbook-view** — Replace list layout with feed layout using feed-item component, applying brand-kit styles (depends: create-feed-item-component)

5. **add-content-type-indicators** — Use visual cues (icon, badge, or card styling) to differentiate photos from text posts in feed (depends: redesign-guestbook-view)

6. **write-feature-tests** — Add tests for guestbook feed aggregation, ordering, user association, and engagement count display (depends: add-content-type-indicators)

---

## Resolution Summary

Not started. Proposal defines guestbook refactor to unified social feed with user avatars and engagement preview.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** feature tests for feed logic and rendering
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
