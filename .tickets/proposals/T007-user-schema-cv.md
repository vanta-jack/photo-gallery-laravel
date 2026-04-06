<instructions>
DOCUMENT TYPE: Proposal Ticket (T007-user-schema-cv.md)

This ticket is in PROPOSAL status. No implementation has started yet. The current users table only has basic fields: id, role, email, first_name, last_name, password, profile_photo_id, created_at, updated_at.

This ticket will add CV/About Me fields to support professional profile functionality as specified in .tickets/active/004-site-implementations.md.

STATUS: Awaiting implementation approval and resource allocation.
</instructions>

# T007: User Schema for CV/About Me

**Status:** IN PROGRESS  
**Tag:** `user-schema-cv`

---

## Problem Statement

The users table lacks CV and professional profile fields required for the About Me public page feature. Users cannot currently display bio, contact information, academic history, work experience, or skills on their profile.

## Specifications

### Feature Requirements

1. **Professional Summary/Bio** — Users can write a markdown-formatted professional summary (up to 5000 characters) displayed on their public profile
2. **Contact Information** — Users can provide phone number (international format, toggleable private/public) and LinkedIn profile URL
3. **Academic History** — Users can add multiple education entries (institution, degree type, graduation year) displayed in chronological order
4. **Professional Experience** — Users can add multiple work history entries (title, company, years, description) automatically sorted in reverse chronological order
5. **Skills & Qualifications** — Users can add skill tags with auto-suggestions from a predefined library, accepting custom entries when no match exists
6. **Optional Fields** — Support for certification photos, ORCID ID, GitHub profile, and other professional links

### Technical Requirements

- **Database:** Add 10 new columns to `users` table via migration
  - `bio` (text, nullable) — Professional summary
  - `phone` (varchar 20, nullable) — International format phone
  - `phone_public` (boolean, default false) — Phone visibility toggle
  - `linkedin` (varchar 255, nullable) — LinkedIn profile URL
  - `academic_history` (json, nullable) — Array of education objects
  - `professional_experience` (json, nullable) — Array of work history objects
  - `skills` (json, nullable) — Array of skill strings
  - `certifications` (json, nullable) — Array of certification objects
  - `orcid_id` (varchar 50, nullable) — ORCID identifier
  - `github` (varchar 255, nullable) — GitHub profile URL
  - `other_links` (json, nullable) — Array of additional links

- **Routes:** Update existing user profile edit route to handle new fields
- **Dependencies:** EasyMDE markdown editor (already in codebase for albums)
- **Files:**
  - Migration: `database/migrations/YYYY_MM_DD_add_cv_fields_to_users_table.php`
  - Model: `app/Models/User.php` (update fillable and casts)
  - Form Request: `app/Http/Requests/UpdateUserRequest.php` (add validation)
  - View: `resources/views/users/edit.blade.php` (add form sections)
  - Controller: `app/Http/Controllers/UserController.php` (handle JSON arrays)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **create-migration** — Create migration file adding 10 CV fields to users table (bio, phone, phone_public, linkedin, academic_history, professional_experience, skills, certifications, orcid_id, github, other_links)

2. **update-model** — Update User model with new fillable fields and JSON array casts for academic_history, professional_experience, skills, certifications, other_links (depends: create-migration)

3. **update-form-request** — Add validation rules to UpdateUserRequest for CV fields with nested array validation for JSON columns (depends: update-model)

4. **update-profile-view** — Add form sections to users/edit.blade.php for CV fields using repeatable input groups for arrays, EasyMDE for bio, phone toggle visibility (depends: update-form-request)

5. **update-controller** — Modify UserController to handle JSON array submissions and phone visibility toggle (depends: update-profile-view)

6. **run-migration** — Execute migration to apply schema changes to database (depends: create-migration)

7. **write-tests** — Feature tests for CV field validation, JSON array handling, phone visibility toggle (depends: update-controller)

---

## Resolution Summary

[Fill in AFTER starting work: One sentence summarizing what was built and status.]

### Delivery Overview
- **Core Features (7 todos):** [feature list]
- **Refinements (0 todos):** None yet (add as discovered)
- **Test Coverage:** 0/0 tests
- **Quality:** [Build status, security constraints, etc.]

---

## Refinements Archive

[Refinements appear here as discovered during implementation. Add items as unresolved requirements emerge.]

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
