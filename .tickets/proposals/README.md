# Tickets Index

Summary of all proposal tickets created from `.tickets/active/004-site-implementations.md` analysis.

## Ticket Status

| ID | Title | Priority | Status | Dependencies |
|----|-------|----------|--------|--------------|
| T001 | Brand Kit Foundation | High | Resolved | None |
| T002 | Image Cropping (Frontend) | High | Resolved | T003 |
| T003 | Image Processor (Changed) | High | Resolved, with subs | None |
| T004 | Photo Slide Mode | Medium | Not Written | T001 |
| T005 | Splash Page | Medium | Not Written | T001 |
| T006 | Guestbook as Feed | Medium | Not Written | T001 |
| T007 | User Schema for CV | Medium | Proposal | None |
| T008 | About Me Public Page | Medium | Not Written | T001, T007 |
| T009 | Contact Modal | Low | Not Written | T008 |
| T010 | Admin Dashboard | Medium | Proposal | T001 |
| T011 | Engagement Metrics | Medium | Not Written | None |
| T012 | AI Image Generator | Medium | Proposal | T001, T003 |
| T013 | Cookie Sessions | High | Not Written | None |
| T014 | Photo Analytics | Medium | Not Written | T001 |
| T015 | Milestones Seeder | Low | Not Written | None |
| T016 | Video Support | Low | Not Written | None |
| T017 | Multi-Image Upload | Medium | Not Written | T003 |
| T018 | User Directories | Low | Not Written | T003 |

## Written Proposals (Detailed)

### Foundation Tier
- **[T001-brand-kit-foundation.md](T001-brand-kit-foundation.md)** - Complete VANITI FAIRE design system
- **[T002-image-cropping.md](T002-image-cropping.md)** - Cropper.js frontend integration
- **[T003-image-processor.md](T003-image-processor.md)** - WebP conversion, metadata strip, compression

### Feature Tier
- **[T007-user-schema-cv.md](T007-user-schema-cv.md)** - CV fields for About Me functionality.

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
- **[T012-ai-generator.md](T012-ai-generator.md)** - Experimental Gemini image generation

## Tickets Not Yet Written (Summaries)

### T004: Photo Slide Mode
Fullscreen slideshow viewer with keyboard as well as touch screen button navigation. Toggle between grid and slideshow. Depends on T001 for styling.

### T005: Animated Splash Page
Vaniti Faire logo animation, should be a pop-up box when user enters the first time. Depends on T001 for styling.

### T006: Guestbook as Global Feed
Refactor guestbook to aggregate posts/photos as feed with user avatars. Depends on T001.

### T008: About Me Public Page
Public user profile at `/users/{user}` displaying CV fields. Depends on T001 and T007.

### T009: Contact Me Modal
Contact button on About Me page opening HTML dialog with contact info. Depends on T008.

### T011: User Post Engagement Metrics
Display engagement stats (votes, comments) on user profile. No dependencies.

### T013: Cookie-Based Sessions
Theme persistence via cookie across browser sessions. ThemeController updates.

### T014: Graphical Analysis - Photo Ratings
Analytics page with top rated and most commented photos. Depends on T001.

### T015: Milestones Seed Data Structure
Seeder for milestone placeholders (baby, grade school, college stages).

### T016: Video Integration
Add video support to photos with `type` column and conditional rendering.

### T017: Multi-Image Upload
Batch upload multiple images at once. Depends on T003.

### T018: User Directories for Images
Organize uploads in `photos/{user_id}/` directories. Depends on T003.

## Recommended Implementation Order

### Phase 1 - Foundation (Start Here)
1. **T001** Brand Kit Foundation (blocks most UI work)
2. **T003** Image Processor (blocks proper image handling)
3. **T002** Image Cropping (depends on T003)
4. **T013** Cookie Sessions

### Phase 2 - Core Features
5. **T007** User Schema for CV
6. **T008** About Me Public Page
7. **T004** Photo Slide Mode
8. **T005** Animated Splash Page

### Phase 3 - Social & Analytics
9. **T006** Guestbook as Global Feed
10. **T010** Admin Dashboard
11. **T011** Engagement Metrics
12. **T014** Photo Analytics

### Phase 4 - Advanced & Polish
13. **T012** AI Image Generator
14. **T009** Contact Modal
15. **T017** Multi-Image Upload
16. **T018** User Directories
17. **T015** Milestones Seeder
18. **T016** Video Integration

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
