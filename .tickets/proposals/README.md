# Tickets Index

Summary of all proposal tickets created from `.tickets/active/004-site-implementations.md` analysis.

## Ticket Status

| ID | Title | Priority | Status | Dependencies |
|----|-------|----------|--------|--------------|
| T001 | Brand Kit Foundation | High | Resolved | None |
| T002 | Image Cropping (Frontend) | High | Resolved | T003 |
| T003 | Image Processor (Changed) | High | Resolved, with subs | None |
| T004 | Photo Slide Mode | Medium | Proposal | T001 |
| T005 | Splash Page | Medium | Proposal | T001 |
| T006 | Guestbook as Feed | Medium | Proposal | T001 |
| T007 | User Schema for CV | Medium | Proposal | None |
| T008 | About Me Public Page + Contact Modal | Medium | Proposal | T001, T007 |
| T009 | Contact Modal | Low | Merged into T008 | T008 |
| T010 | Admin Dashboard | Medium | Proposal | T001 |
| T011 | Engagement Metrics | Medium | Proposal | None |
| T012 | AI Image Generator | Medium | Proposal | T001, T003 |
| T013 | Cookie Sessions | High | Proposal | None |
| T014 | Photo Analytics | Medium | Proposal | T001 |
| T015 | Milestones Seeder | Low | Proposal | None |
| T016 | Video Support | Low | IGNORED | None |
| T017 | Multi-Image Upload | Medium | Proposal | T003 |
| T018 | User Directories | Low | IGNORED | T003 |
| T019 | Clean Database & Demo Seed | High | Proposal | None |

## Written Proposals (Detailed)

### Foundation Tier
- **[T001-brand-kit-foundation.md](T001-brand-kit-foundation.md)** - Complete VANITI FAIRE design system
- **[T002-image-cropping.md](T002-image-cropping.md)** - Cropper.js frontend integration
- **[T003-image-processor.md](T003-image-processor.md)** - WebP conversion, metadata strip, compression

### Feature Tier
- **[T004-photo-slide-mode.md](T004-photo-slide-mode.md)** - Fullscreen slideshow with keyboard and touch navigation
- **[T005-splash-page.md](T005-splash-page.md)** - Animated splash modal with VNT logo on first visit
- **[T006-guestbook-global-feed.md](T006-guestbook-global-feed.md)** - Unified social feed with posts/photos and user avatars
- **[T007-user-schema-cv.md](T007-user-schema-cv.md)** - CV fields for About Me functionality
- **[T008-about-me-public-page.md](T008-about-me-public-page.md)** - Public About Me page with integrated Contact Modal (includes original T009 scope)

Update these in the schema for the user
  - Expertise Field: Use a long list of possible expertise publicly sourced library and just let user type and select like tag selection.
  - Contact information
    - Name (already provided)
    - Phone Number - international format - must be toggleable from private to public
    - LinkedIn Profile - accept linkedin link
  - Bio - can use markdown implementation here. use MDEase which is already available in the codebase here just reimplement that.
  - Academic history
    - Fully customizable template, user can add as many as they like
      - Institution/School - plaintext
      - Degree - Senior High School, Associate, Undergraduate Program, etc.
      - Professional Experience - template fully customizable and can add as much as they like
        - standard work history forms and accomplishments
        - automatically sorted in reverse order upon hitting update profile
      - Skills & Qualifications - users can type like tags which can give auto-suggestions. when it does not exist accept user input.
      - Optionals
        - uploaded photo for certifications, ORCID ID, GitHub profile, other links
- **[T010-admin-dashboard.md](T010-admin-dashboard.md)** - User analytics for admins
- **[T011-engagement-metrics.md](T011-engagement-metrics.md)** - User engagement stats (votes, comments, ratings over time)
- **[T012-ai-generator.md](T012-ai-generator.md)** - Experimental Gemini image generation
- **[T013-cookie-sessions.md](T013-cookie-sessions.md)** - Cookie-based theme and session persistence
- **[T014-photo-analytics.md](T014-photo-analytics.md)** - Photo analytics dashboard (top-rated, most-commented)
- **[T015-milestones-seeder.md](T015-milestones-seeder.md)** - Milestone seed data for lifecycle stages
- **[T017-multi-image-upload.md](T017-multi-image-upload.md)** - Batch upload with preview grid and progress indication
- **[T019-clean-database-seed.md](T019-clean-database-seed.md)** - Clean database reset with demo user accounts

## Implementation Notes

### T009: Contact Me Modal
Merged into T008 to be implemented in the same delivery pass on the About Me page.

### IGNORED Tickets
- **T016: Video Integration** - Out of scope for current phase
- **T018: User Directories for Images** - Out of scope for current phase

## Recommended Implementation Order

### Phase 1 - Foundation (Completed)
1. ✅ **T001** Brand Kit Foundation
2. ✅ **T003** Image Processor
3. ✅ **T002** Image Cropping

### Phase 2 - Core Features (In Progress)
4. **T007** User Schema for CV (Proposal ready)
5. **T008** About Me Public Page + Contact Modal (Proposal ready)
6. **T013** Cookie Sessions (Proposal ready)
7. **T004** Photo Slide Mode (Proposal ready)
8. **T005** Animated Splash Page (Proposal ready)

### Phase 3 - Social & Analytics
9. **T006** Guestbook as Global Feed (Proposal ready)
10. **T010** Admin Dashboard (Proposal ready)
11. **T011** Engagement Metrics (Proposal ready)
12. **T014** Photo Analytics (Proposal ready)

### Phase 4 - Advanced & Polish
13. **T012** AI Image Generator (Proposal ready)
14. **T017** Multi-Image Upload (Proposal ready)
15. **T015** Milestones Seeder (Proposal ready)

### Phase 5 - Demo Preparation
16. **T019** Clean Database & Demo Seed (Proposal ready - HIGH PRIORITY before demo)

## Technology Notes

- **Framework:** Laravel 13.3.0, PHP 8.5
- **Database:** SQLite
- **Frontend:** Tailwind CSS 4.0 (via Vite), Blade templates
- **Session:** Database driver with cookie for theme persistence
- **Image Processing:** Intervention Image v3
- **Cropping:** Cropper.js
- **Icons:** Lucide (per brand kit)

## Brand Kit Requirements Summary

All UI must follow VANITI FAIRE brand kit:
- Inter font (400, 700 weights)
- Zinc color palette only
- 2px border radius universally
- No shadows anywhere
- Dark mode default
- VNT logo in footer only
- Institutional voice (no "welcome", no exclamations)
- EXPERIMENTAL badge + disclaimer for unstable features
