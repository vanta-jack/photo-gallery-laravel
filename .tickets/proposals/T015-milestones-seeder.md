# T015: Milestones Seed Data Structure

**Status:** IN PROGRESS  
**Tag:** `milestones-seeder`

---

## Problem Statement

The milestones feature exists in the schema and UI but lacks seed data to demonstrate lifecycle stages (baby, grade school, college) for testing and demo purposes.

## Specifications

### Feature Requirements
1. **Lifecycle stage seeds** — Create milestone placeholders for common life stages: baby, toddler, preschool, grade school, middle school, high school, college, adult milestones
2. **User association** — Seed milestones for existing seeded users (e.g., user@domain.com, admin@domain.com)
3. **Photo placeholders** — Associate milestones with seeded photos when available, or leave photo_id null for text-only milestones
4. **Realistic labels** — Use descriptive labels like "First Steps", "First Day of School", "High School Graduation", "College Degree"
5. **Chronological order** — Seed milestones in logical chronological progression with realistic created_at timestamps
6. **Demo-ready data** — Ensure seed data is polished for tech demo presentation

### Technical Requirements
- **Database:** Use existing milestones table with columns: user_id, photo_id (nullable), stage, label, description, created_at
- **Routes:** No new routes. Seed data supports existing milestone CRUD routes.
- **Dependencies:** No new packages. Use Laravel DatabaseSeeder.
- **Files:**
  - `database/seeders/MilestoneSeeder.php` (create new seeder)
  - `database/seeders/DatabaseSeeder.php` (call MilestoneSeeder)
  - `tests/Feature/MilestoneSeedTest.php` (optional: verify seed data integrity)

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **create-milestone-seeder** — Build MilestoneSeeder class with factory-style data for common lifecycle stages (baby through adult)

2. **define-stage-categories** — Define stage constants or array: 'baby', 'toddler', 'preschool', 'elementary', 'middle_school', 'high_school', 'college', 'adult' (depends: create-milestone-seeder)

3. **associate-with-users** — Seed milestones for at least 2 users (user@domain.com and admin@domain.com) with varied milestone counts (depends: define-stage-categories)

4. **add-realistic-timestamps** — Set created_at timestamps in chronological progression (e.g., baby milestones dated years ago, college milestones more recent) (depends: associate-with-users)

5. **integrate-into-database-seeder** — Call MilestoneSeeder from DatabaseSeeder::run() after UserSeeder and PhotoSeeder (depends: add-realistic-timestamps)

6. **verify-seed-output** — Run php artisan db:seed and manually verify milestone data appears correctly in milestone index views (depends: integrate-into-database-seeder)

---

## Resolution Summary

Not started. Proposal defines milestone seed data for lifecycle stage demonstration.

### Delivery Overview
- **Core Features (6 todos):** planned and pending implementation
- **Refinements (0 todos):** none discovered yet
- **Test Coverage:** optional seed integrity tests
- **Quality:** pending implementation

---

## Refinements Archive

No refinements recorded yet.

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

</archive>
