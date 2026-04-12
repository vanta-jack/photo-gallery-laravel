<instructions>
DOCUMENT TYPE: Proposal Ticket Template (T000-template.md)

This document is a TEMPLATE for creating new proposal tickets. It shows the ACTIVE STATE (how a ticket looks at creation and during work, before resolution).

KEY PRINCIPLES FOR AI AGENTS:

1. ACTIVE CASE (Lines 1-55)
   - This section is visible and editable from CREATION through COMPLETION
   - Users fill Problem Statement, Specifications, and Implementation Todos at creation
   - Users add Refinements as discovered during work
   - Users update Resolution Summary and Delivery Overview as work progresses
   - This section is NEVER archived until ticket is RESOLVED

2. ARCHIVE SECTION (Lines 59-76)
   - STARTS EMPTY during active work (not a mistake—this is correct)
   - Populated ONLY when ticket reaches RESOLVED status
   - When resolved, Problem Statement, Specifications, and Implementation Todos are moved here
   - Sections are prefixed with [History] to show they are archived content
   - Exists to preserve original specs for historical reference without cluttering active section

3. WORKFLOW PHASES
   - PHASE 1 (CREATION): Fill Problem/Specs/Todos. Leave blank: Resolution Summary, Refinements, Archive.
   - PHASE 2 (WORK): Add Refinements as discovered. Update Resolution Summary and Delivery Overview.
   - PHASE 3 (COMPLETE): Mark Refinements ✅. Move active content to Archive with [History] prefix. Set Status: RESOLVED.

4. REFINEMENTS DURING WORK
   - Refinements are unresolved requirements discovered during implementation
   - They appear in "Refinements Archive" section as work progresses
   - Each refinement has: Root issue, Implemented solution, Relevant files, Verification summary
   - Mark each with ✅ RESOLVED when complete
   - Do NOT move refinements to archive—they stay visible until ticket is RESOLVED

5. FOR AI AGENTS USING THIS TEMPLATE
   - When creating a new ticket, copy this file and fill the active section (Problem/Specs/Todos)
   - When working on a ticket, add Refinements to the Refinements Archive section as discovered
   - When completing a ticket, move Problem/Specs/Todos to <archive> section with [History] prefix
   - Keep archive empty until ticket reaches RESOLVED status
   - Use T003C-albums-view.md as a reference for a RESOLVED ticket (shows what archive looks like when populated)

6. DO NOT
   - Archive content before ticket is RESOLVED
   - Leave placeholder text unfilled when creating ticket
   - Mix new active content with archived content
   - Delete archive section during work
   - Assume archive should be full—it starts empty

REFERENCE: See T003C-albums-view.md for a real-world RESOLVED ticket (archive populated).
</instructions>

# TXXX: [Feature/Fix Title]

**Status:** IN PROGRESS  
**Tag:** `feature-name`

---

## Problem Statement

[One sentence: What is broken or missing? Why does it matter?]

## Specifications

### Feature Requirements
1. [Requirement with acceptance criteria]
2. [Requirement with acceptance criteria]
3. [Requirement with acceptance criteria]

### Technical Requirements
- **Database:** [New migrations, schema changes]
- **Routes:** [New routes, authorization checks]
- **Dependencies:** [New packages, versions]
- **Files:** [Controllers, models, views, tests affected]

## Implementation Todos

Listed in order of execution (dependencies noted):

1. **todo-1-name** — What this accomplishes and acceptance criteria
2. **todo-2-name** — What this accomplishes and acceptance criteria (depends: todo-1)
3. **todo-3-name** — What this accomplishes and acceptance criteria

---

## Resolution Summary

[Fill in AFTER starting work: One sentence summarizing what was built and status.]

### Delivery Overview
- **Core Features (N todos):** [feature list]
- **Refinements (N todos):** [refinement list] (add as discovered)
- **Test Coverage:** X/Y tests
- **Quality:** [Build status, security constraints, etc.]

---

## Refinements Archive

[Refinements appear here as discovered during implementation. Add items as unresolved requirements emerge.]

### ✅ [Refinement 1 Title] (example format, delete this section if no refinements)
- **Root issue:** [What problem does this solve?]
- **Implemented solution:** [What was built? Key files and changes.]
- **Relevant files touched:** `file1.php`, `file2.blade.php`, `file3.js`
- **Verification summary:** [How was it tested? Assert: ✅ RESOLVED]

---

<archive>

This section stays empty during active work. Populate ONLY when ticket is RESOLVED.

When resolved, move Problem Statement, Specifications, and Implementation Todos here (after marking all Refinements ✅).

Example structure (fill in your details):

## [History] Problem Statement
[Original problem statement for reference]

## [History] Specifications
[Original specs and requirements]

## [History] Implementation Todos
[Original todo list and dependencies]

</archive>
