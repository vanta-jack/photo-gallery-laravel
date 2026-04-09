# T011: User Post Engagement Metrics

**Status:** RESOLVED  
**Tag:** `engagement-metrics`

---

## Problem Statement

User profiles lack engagement analytics display, preventing users from tracking votes, comments, and interaction trends on their posts and photos over time.

## Specifications

### Feature Requirements
1. **Engagement stats summary** — Display total votes, comments, and ratings on user profile page
2. **Top content highlights** — Show user's most-voted post and most-rated photo with engagement counts
3. **Engagement over time** — Display a simple chart or list showing engagement trends (e.g., votes/comments per week/month)
4. **Profile integration** — Add engagement section to existing profile edit or About Me page
5. **Public visibility** — Engagement stats visible on public user profile (users.show from T008)
6. **Brand-compliant design** — Follow VANITI FAIRE brand kit with simple bar charts or text-based metrics

### Technical Requirements
- **Database:** No new migrations. Use existing post_votes, photo_ratings, and photo_comments tables.
- **Routes:** Enhance existing user profile routes (profile.edit or users.show) to include engagement data.
- **Dependencies:** No new packages. Use Laravel query builder for aggregations.
- **Files:**
  - `app/Http/Controllers/UserController.php` (add engagement queries to show/edit methods)
  - `resources/views/users/partials/engagement-stats.blade.php` (engagement metrics component)
  - `resources/views/users/show.blade.php` (integrate engagement stats - if T008 exists)
  - `resources/views/users/edit.blade.php` (optional: show own engagement stats)
  - `tests/Feature/UserEngagementMetricsTest.php` (feature tests for aggregation logic)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **implement-engagement-queries** — Add methods to UserController to aggregate total votes, comments, ratings, and identify top content (highest vote count post, highest rated photo)

2. **create-engagement-stats-component** — Build Blade partial to display engagement summary with counts, top content cards, and simple bar chart or timeline (depends: implement-engagement-queries)

3. **integrate-into-profile-views** — Add engagement stats component to users.show (public profile) and optionally to profile.edit (private view) (depends: create-engagement-stats-component)

4. **add-over-time-metrics** — Implement query to group engagement by time period (week/month) and display as list or simple chart (depends: integrate-into-profile-views)

5. **style-with-brand-kit** — Apply zinc palette, 2px radius, no shadows, Inter font to all engagement UI components (depends: add-over-time-metrics)

6. **write-feature-tests** — Test engagement aggregation accuracy, top content identification, and time-period grouping (depends: style-with-brand-kit)

---

## Resolution Summary

Not started. Proposal defines user engagement metrics display for posts and photos.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** feature tests for engagement calculations
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
