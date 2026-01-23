# Tasks: Upgrade to Filament PHP v5 and Package Upgrades

**Input**: Design documents from `/specs/001-filament-v5-upgrade/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are included as they are required by constitution (unit, feature, integration, manual testing).

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Laravel modules**: `modules/default/[module-name]/src/` (catalog, content, users, reports, utilities)
- **Filament panels**: `panels/[panel-name]/src/` (default, providers)
- **App code**: `app/` (models, controllers, services)
- **Tests**: `tests/` (Feature, Unit, Integration)
- **Migrations**: `database/migrations/`
- **Configuration**: `composer.json`, `composer.lock`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and upgrade preparation

- [x] T001 Create upgrade branch and verify git status in repository root
- [x] T002 [P] Create backup of composer.json as composer.json.backup in repository root
- [x] T003 [P] Create backup of composer.lock as composer.lock.backup in repository root
- [x] T004 [P] Create upgrade documentation directory specs/001-filament-v5-upgrade/upgrade-notes/

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T005 Create database backup using backup system (manual step - document in upgrade-notes/)
- [x] T006 [P] Verify PHP version compatibility (8.1+) in development environment
- [x] T007 [P] Verify Laravel version compatibility (10.1+) in development environment
- [x] T008 [P] Create audit script to document custom Filament components in scripts/audit-filament-components.php
- [x] T009 [P] Create plugin compatibility checker script in scripts/check-plugin-compatibility.php
- [x] T010 Setup rollback procedure documentation in specs/001-filament-v5-upgrade/upgrade-notes/rollback-procedure.md

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - System Administrator Performs Package Upgrade (Priority: P1) 🎯 MVP

**Goal**: Upgrade Filament from v3.2 to v5 and upgrade Filament-related and security-critical packages to their latest compatible versions

**Independent Test**: Can be fully tested by running the upgrade process in a development environment, verifying all packages upgrade successfully, and confirming the application starts without errors

### Tests for User Story 1

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T011 [P] [US1] Create unit test for package compatibility checker in tests/Unit/PackageCompatibilityTest.php
- [ ] T012 [P] [US1] Create integration test for composer update process in tests/Integration/ComposerUpdateTest.php
- [ ] T013 [US1] Create feature test for application startup after upgrade in tests/Feature/ApplicationStartupAfterUpgradeTest.php

### Implementation for User Story 1

#### Pre-Upgrade Audit Stage

- [x] T014 [US1] Run audit script to document all custom Filament components and output to specs/001-filament-v5-upgrade/upgrade-notes/component-audit.md
- [x] T015 [P] [US1] Document all Filament resources in panels/default/src/Resources/ and panels/providers/src/Filament/Resources/ to specs/001-filament-v5-upgrade/upgrade-notes/component-audit.md
- [x] T016 [P] [US1] Document all Filament pages in panels/default/src/Pages/ and panels/providers/src/Filament/Pages/ to specs/001-filament-v5-upgrade/upgrade-notes/component-audit.md
- [x] T017 [P] [US1] Document all Filament widgets in panels/default/src/ and panels/providers/src/Filament/Widgets/ to specs/001-filament-v5-upgrade/upgrade-notes/component-audit.md
- [x] T018 [US1] Run plugin compatibility checker script and output to specs/001-filament-v5-upgrade/upgrade-notes/plugin-compatibility-matrix.md
- [x] T019 [US1] Check security advisories using composer audit and document in specs/001-filament-v5-upgrade/upgrade-notes/security-advisories.md

#### Dependency Upgrade Stage

- [x] T020 [US1] Identify security-critical packages (advisories OR vulnerabilities) from security-advisories.md
- [ ] T021 [P] [US1] Update security-critical package versions in composer.json (one package per task if parallelizable)
- [ ] T022 [US1] Run composer update for security-critical packages with composer update [package-name] --with-dependencies
- [ ] T023 [US1] Verify dependency compatibility after security-critical upgrades in composer.json
- [ ] T024 [US1] Run tests after security-critical package upgrades with php artisan test

#### Core Filament Upgrade Stage

- [ ] T025 [US1] Update filament/filament version constraint to ^5.0 in composer.json
- [ ] T026 [US1] Update filament/tables version constraint to ^5.0 in composer.json
- [ ] T027 [US1] Update filament/infolists version constraint to ^5.0 in composer.json
- [ ] T028 [US1] Update Livewire version if required for Filament v5 compatibility in composer.json
- [ ] T029 [US1] Run composer update to upgrade Filament core packages
- [ ] T030 [US1] Verify application starts without fatal errors using php artisan serve
- [ ] T031 [US1] Check for immediate errors in storage/logs/laravel.log after Filament upgrade

#### Plugin Compatibility Verification Stage

- [ ] T032 [US1] Verify bezhansalleh/filament-shield has v5-compatible version and update in composer.json
- [ ] T033 [US1] Verify bezhansalleh/filament-language-switch has v5-compatible version and update in composer.json
- [ ] T034 [US1] Verify cheesegrits/filament-google-maps has v5-compatible version and update in composer.json
- [ ] T035 [US1] Verify ysfkaya/filament-phone-input has v5-compatible version and update in composer.json
- [ ] T036 [US1] Verify pxlrbt/filament-activity-log has v5-compatible version and update in composer.json
- [ ] T037 [US1] Verify pxlrbt/filament-excel has v5-compatible version and update in composer.json
- [ ] T038 [US1] Verify saade/filament-fullcalendar has v5-compatible version and update in composer.json
- [ ] T039 [US1] Verify filament/spatie-laravel-media-library-plugin has v5-compatible version and update in composer.json
- [ ] T040 [US1] Verify filament/spatie-laravel-settings-plugin has v5-compatible version and update in composer.json
- [ ] T041 [US1] Verify filament/spatie-laravel-translatable-plugin has v5-compatible version and update in composer.json
- [ ] T042 [US1] Verify filament/spatie-laravel-google-fonts-plugin has v5-compatible version and update in composer.json
- [ ] T043 [US1] If any critical plugin is incompatible, document and STOP upgrade per clarification (delay until compatible)
- [ ] T044 [US1] Run composer update to upgrade all compatible Filament plugins
- [ ] T045 [US1] Verify plugin compatibility after upgrade in composer.lock

**Checkpoint**: At this point, User Story 1 should be fully functional and testable independently - all packages upgraded, application starts successfully

---

## Phase 4: User Story 2 - Developer Verifies Filament Panel Functionality (Priority: P2)

**Goal**: Verify that all Filament admin panels continue to function correctly after the upgrade, ensuring no breaking changes affect the user interface or functionality

**Independent Test**: Can be fully tested by accessing each Filament panel (default admin, providers), verifying all resources, pages, and widgets load correctly, and testing key workflows like creating, editing, and viewing records

### Tests for User Story 2

- [ ] T046 [P] [US2] Create feature test for default admin panel accessibility in tests/Feature/DefaultAdminPanelTest.php
- [ ] T047 [P] [US2] Create feature test for providers panel accessibility in tests/Feature/ProvidersPanelTest.php
- [ ] T048 [P] [US2] Create feature test for Filament resource loading in tests/Feature/FilamentResourceLoadingTest.php
- [ ] T049 [P] [US2] Create feature test for Filament page loading in tests/Feature/FilamentPageLoadingTest.php
- [ ] T050 [P] [US2] Create feature test for Filament widget rendering in tests/Feature/FilamentWidgetRenderingTest.php

### Implementation for User Story 2

#### Custom Code Update Stage

- [ ] T051 [US2] Review component audit report from specs/001-filament-v5-upgrade/upgrade-notes/component-audit.md
- [ ] T052 [P] [US2] Update custom Filament resources in panels/default/src/Resources/ for v5 API compatibility (one resource per task)
- [ ] T053 [P] [US2] Update custom Filament resources in panels/providers/src/Filament/Resources/ for v5 API compatibility (one resource per task)
- [ ] T054 [P] [US2] Update custom Filament pages in panels/default/src/Pages/ for v5 API compatibility
- [ ] T055 [P] [US2] Update custom Filament pages in panels/providers/src/Filament/Pages/ for v5 API compatibility
- [ ] T056 [P] [US2] Update custom Filament widgets in panels/default/src/ for v5 API compatibility
- [ ] T057 [P] [US2] Update custom Filament widgets in panels/providers/src/Filament/Widgets/ for v5 API compatibility
- [ ] T058 [P] [US2] Update custom form components in panels/default/src/Forms/Components/ for v5 API compatibility
- [ ] T059 [US2] Resolve all deprecation warnings in custom components per FR-012
- [ ] T060 [US2] Document breaking changes and required modifications in specs/001-filament-v5-upgrade/upgrade-notes/breaking-changes.md

#### Panel Functionality Verification

- [ ] T061 [US2] Verify default admin panel loads at /admin route
- [ ] T062 [US2] Verify providers panel loads at /providers route (or configured path)
- [ ] T063 [P] [US2] Test all Filament resources in default admin panel for create, edit, view, delete operations
- [ ] T064 [P] [US2] Test all Filament resources in providers panel for create, edit, view, delete operations
- [ ] T065 [P] [US2] Test all Filament pages in default admin panel for correct rendering
- [ ] T066 [P] [US2] Test all Filament pages in providers panel for correct rendering
- [ ] T067 [P] [US2] Test all Filament widgets in default admin panel for correct data display
- [ ] T068 [P] [US2] Test all Filament widgets in providers panel for correct data display
- [ ] T069 [US2] Verify custom Filament components render without errors in both panels

**Checkpoint**: At this point, User Stories 1 AND 2 should both work independently - packages upgraded and all panels functional

---

## Phase 5: User Story 3 - Quality Assurance Validates System Stability (Priority: P3)

**Goal**: Verify that the upgraded system maintains all existing functionality, performs regression testing, and ensures no features are broken by the upgrade

**Independent Test**: Can be fully tested by running the full test suite, performing manual testing of critical user workflows, and verifying that all existing features work as expected

### Tests for User Story 3

- [ ] T070 [US3] Run full automated test suite with php artisan test and verify 100% pass rate
- [ ] T071 [P] [US3] Create integration test for module interactions in tests/Integration/ModuleInteractionsTest.php
- [ ] T072 [P] [US3] Create integration test for panel-module relationships in tests/Integration/PanelModuleRelationshipsTest.php

### Implementation for User Story 3

#### Database Migration Review Stage

- [ ] T073 [US3] Identify new migrations from upgraded packages using php artisan migrate:status
- [ ] T074 [US3] Review each migration file for data safety in database/migrations/
- [ ] T075 [US3] Document migration review results in specs/001-filament-v5-upgrade/upgrade-notes/migration-review.md
- [ ] T076 [US3] Get manual approval for migrations (document approval in migration-review.md)
- [ ] T077 [US3] Run approved migrations with php artisan migrate
- [ ] T078 [US3] Verify database integrity after migrations using database checks

#### Comprehensive Testing Stage

- [ ] T079 [US3] Run unit tests for custom Filament components with php artisan test --filter Unit
- [ ] T080 [US3] Run feature tests for Filament resources and forms with php artisan test --filter Feature
- [ ] T081 [US3] Run integration tests for module interactions with php artisan test --filter Integration
- [ ] T082 [US3] Perform manual testing of critical admin workflows in default panel
- [ ] T083 [US3] Perform manual testing of critical admin workflows in providers panel
- [ ] T084 [US3] Check application logs for new errors in storage/logs/laravel.log
- [ ] T085 [US3] Check application logs for new warnings in storage/logs/laravel.log
- [ ] T086 [US3] Verify performance metrics (panel load times) meet <500ms target
- [ ] T087 [US3] Document test results in specs/001-filament-v5-upgrade/upgrade-notes/test-results.md

#### Completion Stage

- [ ] T088 [US3] Final verification: all panels functional, all components working, no critical issues
- [ ] T089 [US3] Update breaking changes documentation in specs/001-filament-v5-upgrade/upgrade-notes/breaking-changes.md
- [ ] T090 [US3] Create upgrade completion report in specs/001-filament-v5-upgrade/upgrade-notes/completion-report.md
- [ ] T091 [US3] Document rollback procedures in specs/001-filament-v5-upgrade/upgrade-notes/rollback-procedure.md
- [ ] T092 [US3] Verify all success criteria met (SC-001 through SC-009 from spec.md)

**Checkpoint**: All user stories should now be independently functional - upgrade complete, tested, and validated

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [ ] T093 [P] Update project documentation with Filament v5 upgrade notes in README.md
- [ ] T094 [P] Code cleanup: remove any temporary upgrade scripts in scripts/
- [ ] T095 [P] Verify PSR-12 code standards compliance using php artisan pint
- [ ] T096 [P] Verify all type hints are present in updated files using static analysis
- [ ] T097 [P] Update PHPDoc comments for all updated custom Filament components
- [ ] T098 Performance optimization: verify no performance degradation after upgrade
- [ ] T099 Security hardening: verify all security-critical packages upgraded
- [ ] T100 Run quickstart.md validation: execute upgrade process using quickstart.md guide
- [ ] T101 Final code review: verify constitution compliance for all changes

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User stories can then proceed sequentially in priority order (P1 → P2 → P3)
  - US2 depends on US1 completion (needs upgraded packages to verify)
  - US3 depends on US1 and US2 completion (needs upgraded packages and verified panels)
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Depends on US1 completion - Needs upgraded packages to verify functionality
- **User Story 3 (P3)**: Depends on US1 and US2 completion - Needs upgraded packages and verified panels to test

### Within Each User Story

- Tests MUST be written and FAIL before implementation (where applicable)
- Audit before upgrade
- Dependencies before core
- Core before plugins
- Plugins before custom code
- Custom code before testing
- Story complete before moving to next priority

### Parallel Opportunities

- All Setup tasks marked [P] can run in parallel (T002, T003, T004)
- All Foundational tasks marked [P] can run in parallel (T006, T007, T008, T009)
- Within US1: Security-critical package upgrades (T021) can be parallel if different packages
- Within US1: Plugin compatibility checks (T032-T042) can be parallel
- Within US2: Custom component updates (T052-T058) can be parallel (different files)
- Within US2: Panel testing (T063-T068) can be parallel (different resources/pages/widgets)
- Within US3: Integration tests (T071, T072) can be parallel
- Polish tasks marked [P] can run in parallel (T093, T094, T095, T096, T097)

---

## Parallel Example: User Story 1

```bash
# Launch all security-critical package upgrades together (if different packages):
Task: "Update security-critical package versions in composer.json"
Task: "Update security-critical package versions in composer.json" (different package)

# Launch all plugin compatibility checks together:
Task: "Verify bezhansalleh/filament-shield has v5-compatible version"
Task: "Verify bezhansalleh/filament-language-switch has v5-compatible version"
Task: "Verify cheesegrits/filament-google-maps has v5-compatible version"
# ... etc for all plugins
```

---

## Parallel Example: User Story 2

```bash
# Launch all custom component updates together (different files):
Task: "Update custom Filament resources in panels/default/src/Resources/ for v5 API"
Task: "Update custom Filament resources in panels/providers/src/Filament/Resources/ for v5 API"
Task: "Update custom Filament pages in panels/default/src/Pages/ for v5 API"
Task: "Update custom Filament widgets in panels/default/src/ for v5 API"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1 (Package Upgrade)
4. **STOP and VALIDATE**: Test User Story 1 independently
   - Verify packages upgraded
   - Verify application starts
   - Verify no fatal errors
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy/Demo (MVP - packages upgraded!)
3. Add User Story 2 → Test independently → Deploy/Demo (Panels verified!)
4. Add User Story 3 → Test independently → Deploy/Demo (Full validation!)
5. Each story adds value without breaking previous stories

### Sequential Strategy (Recommended for Upgrade)

With upgrade complexity, sequential approach is recommended:

1. Team completes Setup + Foundational together
2. Complete User Story 1 (Package Upgrade) - CRITICAL PATH
3. Complete User Story 2 (Panel Verification) - Depends on US1
4. Complete User Story 3 (QA Validation) - Depends on US1 and US2
5. Complete Polish phase

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Verify tests fail before implementing (where applicable)
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence
- Upgrade is sequential by nature - US2 and US3 depend on US1 completion
- Manual testing is required per constitution - cannot be fully automated
- Rollback capability must be maintained at each stage

