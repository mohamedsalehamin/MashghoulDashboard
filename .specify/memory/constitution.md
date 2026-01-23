<!--
Sync Impact Report:
Version change: 1.0.0 → 1.1.0 (MINOR: Added upgrade strategy principles)
Modified principles: None (new constitution)
Added sections: Package Upgrade Strategy, Filament v5 Migration
Removed sections: None
Templates requiring updates:
  ✅ .specify/templates/plan-template.md - Updated to reference Laravel/Filament context
  ✅ .specify/templates/spec-template.md - Updated for Laravel feature specs
  ✅ .specify/templates/tasks-template.md - Updated for Laravel task structure
Follow-up TODOs: None
-->

# Mashghoul Constitution

## Core Principles

### I. Laravel-Filament Architecture (NON-NEGOTIABLE)
All admin interfaces MUST be built using Filament panels. The application follows a modular architecture with:
- **Panels**: Admin interfaces (default, providers) in `panels/` directory
- **Modules**: Business logic modules (catalog, content, users, reports, utilities) in `modules/default/` directory
- **Separation**: Panels depend on modules, modules MUST NOT depend on panels
- **Multi-tenancy**: Application MUST support multi-tenant architecture via stancl/tenancy

### II. Package Upgrade Strategy (NON-NEGOTIABLE)
Package upgrades MUST follow this priority:
1. **Security patches**: Apply immediately, no feature work until resolved
2. **Major version upgrades**: Require dedicated upgrade branch with full testing
3. **Filament upgrades**: Currently targeting v5 migration from v3.2
   - All Filament plugins MUST be compatible with target Filament version
   - Custom Filament components MUST be tested against upgrade target
   - Breaking changes MUST be documented before upgrade begins
4. **Dependency compatibility**: Before upgrading any package, verify all dependencies support the new version
5. **Testing requirement**: All upgrades MUST include regression testing of affected features

### III. Module Independence
Each module in `modules/default/` MUST:
- Be self-contained with clear boundaries
- Define its own models, services, and business logic
- Expose functionality via well-defined interfaces
- NOT directly depend on other modules (use dependency injection for cross-module needs)
- Include its own migrations and seeders

### IV. Panel-Module Relationship
Panels MUST:
- Act as presentation layers only
- Delegate business logic to modules
- Use Filament resources, pages, and widgets for UI
- NOT contain business logic (move to appropriate module)
- Support multiple panels accessing the same module functionality

### V. Testing Discipline
- **Unit tests**: Required for all service classes and business logic
- **Feature tests**: Required for all Filament resources and forms
- **Integration tests**: Required for module interactions and API endpoints
- **Manual testing**: Required for Filament panel workflows before deployment
- Test coverage MUST be maintained or improved with each feature addition

### VI. Code Quality Standards
- **PSR-12**: All PHP code MUST follow PSR-12 coding standards
- **Type hints**: All method parameters and return types MUST be explicitly typed
- **Documentation**: All public methods and classes MUST have PHPDoc comments
- **Naming**: Follow Laravel conventions (PascalCase for classes, camelCase for methods, snake_case for database)
- **Deprecation warnings**: MUST be resolved or suppressed with proper justification

## Package Upgrade Requirements

### Filament v5 Migration Strategy
When upgrading to Filament v5:
1. **Pre-upgrade audit**: Document all custom Filament components, resources, and pages
2. **Plugin compatibility check**: Verify all Filament plugins support v5
3. **Breaking changes review**: Study Filament v5 upgrade guide and identify required changes
4. **Incremental migration**: Upgrade in stages (dependencies → core → plugins → custom code)
5. **Testing phase**: Full regression testing of all admin panel functionality
6. **Rollback plan**: Maintain ability to rollback if critical issues discovered

### General Package Upgrade Process
1. Create feature branch: `upgrade/[package-name]-[version]`
2. Update `composer.json` with new version constraint
3. Run `composer update [package-name] --with-dependencies`
4. Review changelog and breaking changes
5. Fix compatibility issues
6. Run test suite
7. Manual testing of affected features
8. Update documentation if API changes
9. Merge only after all tests pass and manual verification complete

## Development Workflow

### Feature Development
- All new features MUST be developed in feature branches
- Features MUST include appropriate tests (unit, feature, or integration)
- Filament resources MUST follow Filament best practices and patterns
- Database changes MUST use migrations (never modify existing migrations)
- Code MUST pass static analysis (Laravel Pint, PHPStan if configured)

### Code Review Requirements
- All PRs MUST be reviewed before merge
- Reviewers MUST verify:
  - Constitution compliance
  - Test coverage maintained/improved
  - No breaking changes without documentation
  - Filament patterns followed correctly
  - Module boundaries respected

### Deployment Process
- Deployments MUST include database migration execution plan
- Cache clearing strategy MUST be documented
- Rollback procedures MUST be tested and documented
- Environment-specific configurations MUST be verified

## Governance

This constitution supersedes all other development practices. All team members MUST comply with these principles.

**Amendment Process**:
- Amendments require team discussion and consensus
- Version number MUST be incremented per semantic versioning:
  - **MAJOR**: Breaking principle changes or removals
  - **MINOR**: New principles or significant expansions
  - **PATCH**: Clarifications, wording improvements, non-semantic changes
- Amendments MUST be documented with rationale
- All templates and related documentation MUST be updated to reflect changes

**Compliance**:
- All PRs/reviews MUST verify constitution compliance
- Violations MUST be justified or corrected before merge
- Complexity additions MUST be justified against simplicity principle
- Package upgrades MUST follow defined upgrade strategy

**Version**: 1.1.0 | **Ratified**: 2026-01-22 | **Last Amended**: 2026-01-22
