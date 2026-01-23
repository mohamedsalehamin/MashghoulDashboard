# Quickstart: Filament v5 Upgrade

**Feature**: Upgrade to Filament PHP v5 and Package Upgrades  
**Date**: 2026-01-22

## Prerequisites

- PHP 8.1+ installed
- Laravel 10.1+ application
- Composer installed
- Git repository with current branch
- Development/staging environment
- Database backup capability
- Access to Filament v5 documentation

## Quick Start (5-Minute Overview)

1. **Audit**: Document all custom Filament components and plugins
2. **Verify**: Check plugin compatibility with Filament v5
3. **Upgrade**: Follow incremental stages (dependencies → core → plugins → custom code)
4. **Test**: Run full test suite and manual testing
5. **Deploy**: Only after all tests pass and validation complete

## Detailed Steps

### Step 1: Pre-Upgrade Audit

```bash
# Create upgrade branch
git checkout -b 001-filament-v5-upgrade

# Backup current state
cp composer.json composer.json.backup
cp composer.lock composer.lock.backup

# Create database backup
php artisan backup:run

# Audit custom components (manual review)
# Document all Filament resources, pages, widgets in:
# - panels/default/src/
# - panels/providers/src/
```

**Checklist**:
- [ ] All custom components documented
- [ ] All Filament plugins listed
- [ ] Security advisories checked
- [ ] Backup created

### Step 2: Check Security Advisories

```bash
# Check for security vulnerabilities
composer audit

# Identify packages with advisories or vulnerabilities
# These are "security-critical" packages
```

**Checklist**:
- [ ] Security-critical packages identified
- [ ] Upgrade priority assigned

### Step 3: Verify Plugin Compatibility

```bash
# For each Filament plugin, check:
# 1. Does v5-compatible version exist?
# 2. Is it critical for functionality?

# Critical plugins to verify:
# - bezhansalleh/filament-shield
# - bezhansalleh/filament-language-switch
# - cheesegrits/filament-google-maps
# - ysfkaya/filament-phone-input
# - pxlrbt/filament-activity-log
# - pxlrbt/filament-excel
# - saade/filament-fullcalendar
# - Spatie Filament plugins
```

**Checklist**:
- [ ] All critical plugins have v5-compatible versions
- [ ] If any incompatible: STOP and wait for compatibility

### Step 4: Upgrade Security-Critical Packages

```bash
# Update composer.json for security-critical packages
# Example:
composer require vendor/package:^new-version

# Update and verify
composer update vendor/package --with-dependencies
php artisan test
```

**Checklist**:
- [ ] Security-critical packages upgraded
- [ ] Tests pass
- [ ] Application starts

### Step 5: Upgrade Filament Core

```bash
# Update Filament packages in composer.json
composer require filament/filament:^5.0
composer require filament/tables:^5.0
composer require filament/infolists:^5.0

# Update dependencies
composer update

# Verify application starts
php artisan serve
```

**Checklist**:
- [ ] Filament upgraded to v5
- [ ] Application starts
- [ ] No fatal errors

### Step 6: Upgrade Filament Plugins

```bash
# Update each plugin to v5-compatible version
composer require bezhansalleh/filament-shield:^v5-compatible
# ... repeat for each plugin

composer update

# Verify
php artisan test
```

**Checklist**:
- [ ] All plugins upgraded
- [ ] Tests pass
- [ ] Panels load

### Step 7: Update Custom Components

```bash
# For each custom component:
# 1. Review v3 API usage
# 2. Update to v5 API
# 3. Test component

# Example: Update a resource
# Review: panels/default/src/Resources/SomeResource.php
# Update deprecated methods
# Test: php artisan test --filter SomeResourceTest
```

**Checklist**:
- [ ] All components updated
- [ ] Deprecation warnings resolved
- [ ] Components tested

### Step 8: Review Database Migrations

```bash
# Check for new migrations from upgraded packages
php artisan migrate:status

# Review each migration
# Approve manually before running

# Run approved migrations
php artisan migrate
```

**Checklist**:
- [ ] Migrations reviewed
- [ ] Only approved migrations run
- [ ] Database integrity verified

### Step 9: Comprehensive Testing

```bash
# Run automated tests
php artisan test

# Manual testing checklist:
# - [ ] Default admin panel loads
# - [ ] Providers panel loads
# - [ ] All resources accessible
# - [ ] Forms submit correctly
# - [ ] Widgets display correctly
# - [ ] Critical workflows functional
```

**Checklist**:
- [ ] All automated tests pass
- [ ] All manual tests pass
- [ ] Performance acceptable
- [ ] No new errors in logs

### Step 10: Final Validation

```bash
# Final checks
php artisan route:list
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Verify panels
# Visit: /admin (default panel)
# Visit: /providers (providers panel)
```

**Checklist**:
- [ ] All routes registered
- [ ] Caches cleared
- [ ] Panels accessible
- [ ] Documentation updated

## Rollback Procedure

If critical issues discovered:

```bash
# Rollback code
git reset --hard HEAD

# Rollback packages
cp composer.json.backup composer.json
cp composer.lock.backup composer.lock
composer install

# Rollback database (if migrations run)
php artisan migrate:rollback

# Restore database backup
# (use your backup tool)
```

## Troubleshooting

### Issue: Plugin incompatible with v5

**Solution**: Delay upgrade until compatible version available (per clarification)

### Issue: Component uses deprecated API

**Solution**: Update component to use v5 API equivalents (check Filament v5 docs)

### Issue: Tests failing

**Solution**: Fix test code for v5 API changes, re-run tests

### Issue: Application won't start

**Solution**: Check error logs, verify all dependencies installed, clear caches

## Success Indicators

- ✅ All packages upgraded
- ✅ All tests pass (100%)
- ✅ All panels functional
- ✅ No critical errors
- ✅ Performance maintained
- ✅ Documentation complete

## Next Steps After Upgrade

1. Monitor production for issues
2. Update team documentation
3. Train team on v5 features
4. Plan future upgrades

