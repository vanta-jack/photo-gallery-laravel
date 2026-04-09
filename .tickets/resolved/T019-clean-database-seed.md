# T019: Clean Database and Demo Seed Data

**Status:** RESOLVED  
**Tag:** `clean-database-seed`

---

## Problem Statement

The database contains development test data that must be removed before demo deployment, with only clean seed data for two accounts (normal user and admin) with generated profile details but no photos.

## Specifications

### Feature Requirements
1. **Database wipe** — Remove all existing data from all tables (truncate or fresh migration)
2. **Normal user account** — Seed user@domain.com with password 'password', generated profile/CV details, no photos
3. **Admin user account** — Seed admin@domain.com with password 'password', role='admin', generated profile/CV details, no photos
4. **Profile data generation** — Use realistic fake data (Faker) for bio, academic history, professional experience, skills
5. **No photo data** — Both accounts start with empty photo galleries to demonstrate clean upload workflow
6. **Reset command** — Provide artisan command or documented process to reset to this clean state for demos

### Technical Requirements
- **Database:** Fresh migration + targeted seeding only (UserSeeder with 2 accounts, no PhotoSeeder/AlbumSeeder/etc.)
- **Routes:** No route changes. This is purely data cleanup.
- **Dependencies:** Use existing Laravel Faker for profile data generation.
- **Files:**
  - `database/seeders/DatabaseSeeder.php` (refactor to seed only 2 users)
  - `database/seeders/UserSeeder.php` (create/update with 2-account seeding logic)
  - `database/factories/UserFactory.php` (update to generate CV fields)
  - `README.md` or `SETUP.md` (document fresh seed command: php artisan migrate:fresh --seed)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **update-user-factory** — Enhance UserFactory to generate realistic bio, academic_history[], professional_experience[], skills[], linkedin, github using Faker

2. **create-clean-user-seeder** — Build UserSeeder that creates exactly 2 users: user@domain.com (role='user') and admin@domain.com (role='admin'), both with generated CV data and password='password' (depends: update-user-factory)

3. **refactor-database-seeder** — Update DatabaseSeeder::run() to call only UserSeeder, removing/commenting out PhotoSeeder, AlbumSeeder, and other data seeders (depends: create-clean-user-seeder)

4. **verify-profile-photo-null** — Ensure both seeded users have profile_photo_id=null to demonstrate empty photo galleries (depends: refactor-database-seeder)

5. **test-fresh-seed** — Run php artisan migrate:fresh --seed and verify only 2 users exist with no photos, albums, posts, etc. (depends: verify-profile-photo-null)

6. **document-reset-process** — Add instructions to README or SETUP.md for resetting to clean demo state before each demo session (depends: test-fresh-seed)

---

## Resolution Summary

Not started. Proposal defines database cleanup and minimal seed data for tech demo presentation.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** manual verification of seeded data
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
