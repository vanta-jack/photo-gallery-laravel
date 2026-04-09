# T014: Graphical Analysis - Photo Ratings

**Status:** RESOLVED  
**Tag:** `photo-analytics`

---

## Problem Statement

The application lacks a dedicated analytics page to visualize photo performance metrics like top-rated photos and most-commented photos, limiting content insights.

## Specifications

### Feature Requirements
1. **Analytics dashboard page** — Create dedicated route/page at /analytics or /photos/analytics displaying photo performance charts
2. **Top-rated photos** — Show photos with highest average ratings in a ranked list with rating scores
3. **Most-commented photos** — Display photos with highest comment counts in a separate ranked list
4. **Visual charts** — Use simple bar charts or horizontal progress bars (pure CSS, brand-kit compliant) to visualize metrics
5. **Photo thumbnails** — Include photo thumbnails with titles/descriptions in analytics lists
6. **Access control** — Determine if analytics page is public (all photos) or private (user's own photos only)
7. **Brand-compliant design** — Follow VANITI FAIRE brand kit: zinc palette, 2px radius, no shadows, Inter font

### Technical Requirements
- **Database:** No new migrations. Query existing photos, photo_ratings, and photo_comments tables with aggregations.
- **Routes:** Add new public or authenticated route for analytics page (e.g., GET /photos/analytics).
- **Dependencies:** No new packages. Use Laravel query builder with aggregations (AVG, COUNT, GROUP BY).
- **Files:**
  - `routes/web.php` (add analytics route)
  - `app/Http/Controllers/PhotoAnalyticsController.php` (create new controller)
  - `resources/views/photos/analytics.blade.php` (analytics dashboard view)
  - `resources/views/photos/partials/analytics-chart.blade.php` (reusable chart component)
  - `tests/Feature/PhotoAnalyticsTest.php` (feature tests for aggregation queries)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **create-analytics-controller** — Build PhotoAnalyticsController with index method that queries top-rated photos (AVG rating DESC) and most-commented photos (comment COUNT DESC)

2. **add-analytics-route** — Register route for analytics page (e.g., /photos/analytics) with appropriate middleware (public or auth) (depends: create-analytics-controller)

3. **create-analytics-view** — Build analytics.blade.php with sections for top-rated and most-commented photos, using card layouts and photo thumbnails (depends: add-analytics-route)

4. **implement-chart-component** — Create reusable analytics-chart.blade.php partial with CSS-only bar charts or horizontal progress bars (depends: create-analytics-view)

5. **style-with-brand-kit** — Apply brand-kit styling to all analytics UI: zinc colors, 2px radius, no shadows, Inter font, institutional voice (depends: implement-chart-component)

6. **write-feature-tests** — Test analytics aggregation queries for accuracy (AVG ratings, comment counts) and correct photo ordering (depends: style-with-brand-kit)

---

## Resolution Summary

Not started. Proposal defines photo analytics dashboard with top-rated and most-commented visualizations.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** feature tests for aggregation accuracy
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
