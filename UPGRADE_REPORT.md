# Laravel 12 Upgrade Report

**Generated:** January 26, 2026  
**Project:** Mashghoul  
**Upgrade:** Laravel 10 → Laravel 12

---

## Executive Summary

The Laravel 12 upgrade has been completed. This report identifies unused files, classes, and configurations that can be safely removed, as well as items that need attention.

---

## 🗑️ Files/Folders to Delete

### 1. Old Project Backup (HIGH PRIORITY)



### 2. Backup/Temporary Files

### 3. Tenancy Leftover Files

| Path | Reason |
|------|--------|
| `storage/tenant_173925cc-0c18-433f-a2c8-d84086751572/` | Leftover from removed tenancy package |

```bash
rm -rf storage/tenant_*/
```

### 4. Old Route File



---

## ⚠️ Unused Classes (Recommend Review)

### Unused Middleware

These middleware classes exist but are **not used** in routes or `bootstrap/app.php`:

| File | Class |
|------|-------|
| `app/Http/Middleware/EnsureThatCartExist.php` | Not referenced anywhere |
| `app/Http/Middleware/EnsureThatReservationDeliveredMiddleware.php` | Not referenced anywhere |
| `app/Http/Middleware/EnsureThatReservationNotScheduleBeforeMiddleware.php` | Not referenced anywhere |
| `app/Http/Middleware/TrustHosts.php` | Commented out, not used |

**Action:** Review if these are needed. If not:
```bash
rm app/Http/Middleware/EnsureThatCartExist.php
rm app/Http/Middleware/EnsureThatReservationDeliveredMiddleware.php
rm app/Http/Middleware/EnsureThatReservationNotScheduleBeforeMiddleware.php
rm app/Http/Middleware/TrustHosts.php
```

### Unused Livewire Components

These Livewire components exist but are **not referenced** in any blade templates:

| File | Class |
|------|-------|
| `app/Livewire/ContactUs.php` | Not found in views |
| `app/Livewire/RegisterButton.php` | Not found in views |
| `app/Livewire/RegisterForm.php` | Not found in views |

**Action:** Verify if these are used via dynamic loading. If not:
```bash
rm -rf app/Livewire/
```

### Potentially Unused Controller

| File | Reason |
|------|--------|
| `app/Http/Controllers/MyFatoorahController.php` | Copied from vendor during upgrade, but routes use vendor package directly |

**Action:** Review if this override is needed. The webhook routes use the library directly.

---

## 📋 Duplicate Dependencies

### Sluggable Packages

You have **two sluggable packages** installed:

| Package | Used By |
|---------|---------|
| `cviebrock/eloquent-sluggable` | `modules/default/content/src/Models/Page.php` |
| `spatie/laravel-sluggable` | `modules/default/content/src/Models/Post.php` |

**Recommendation:** Consolidate to one package. Spatie's package is more actively maintained.

**To consolidate:**
1. Update `Page.php` to use `Spatie\Sluggable\HasSlug`
2. Remove `cviebrock/eloquent-sluggable` from `composer.json`
3. Delete `config/sluggable.php`

---

## 🔧 Laravel 12 Deprecated/Obsolete Files

### Kernel Classes (Obsolete in Laravel 12)

Laravel 12 uses `bootstrap/app.php` for configuration. These kernel files are **no longer needed**:

| File | Status |
|------|--------|
| `app/Http/Kernel.php` | Can be deleted - middleware now in `bootstrap/app.php` |
| `app/Console/Kernel.php` | Can be deleted - scheduling now in `routes/console.php` |

**Note:** Both Kernel files are redundant with the Laravel 12 fluent API. However, keep them if you have backward compatibility concerns.

### Console Scheduling Duplication

Scheduled commands are defined in **both**:
- `app/Console/Kernel.php` (old style)
- `routes/console.php` (Laravel 12 style)

**Action:** The `routes/console.php` is already properly configured. You can delete `app/Console/Kernel.php`.

---

## 📁 Unused Configuration Files

| File | Reason |
|------|--------|
| `config/sluggable.php` | For `cviebrock/eloquent-sluggable` - can be removed if package is consolidated |

---

## 🔍 Scripts (One-time Use)

These scripts were created for the upgrade process:

| File | Purpose |
|------|---------|
| `scripts/audit-filament-components.php` | Filament upgrade audit |
| `scripts/check-plugin-compatibility.php` | Plugin compatibility check |

**Action:** Delete after upgrade is verified stable:
```bash
rm -rf scripts/
```

---

## ✅ Completed Cleanup (Already Done)

The following have already been cleaned up during this session:

| Item | Action Taken |
|------|--------------|
| `stancl/tenancy` | Removed from composer.json |
| `barryvdh/laravel-dompdf` | Removed - using mPDF |
| `mpdf/mpdf` | Removed - redundant with laravel-mpdf |
| `spatie/laravel-pdf` | Removed - unused |
| `config/dompdf.php` | Deleted |
| `config/tenancy.php` | Deleted |
| Tenancy references in code | Removed from `bootstrap/app.php`, `Kernel.php`, `Handler.php` |
| `vendorApiNotToBeDeleted/` | Renamed to `packages/` |
| `tasawk/api` | Now loads from local `packages/` directory |

---

## 📦 Recommended Cleanup Commands

Run these commands to clean up the project:

```bash
# High priority - remove old backup (saves 724MB)
rm -rf old/

# Remove backup files
rm app/Providers/AppServiceProvider.php.save

# Remove tenancy leftovers
rm -rf storage/tenant_*/

# Remove old route file
rm routes/app_old.php

# Clear caches after cleanup
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate autoloader
composer dump-autoload
```

---

## 🔄 Post-Upgrade Tasks

### Required
- [ ] Run `composer update` to apply package changes
- [ ] Test all routes especially `/reservations/{id}/invoice`
- [ ] Verify Arabic PDF rendering with Tajawal font
- [ ] Test payment webhooks (MyFatoorah, Tabby)

### Optional
- [ ] Delete `app/Http/Kernel.php` after confirming app works
- [ ] Delete `app/Console/Kernel.php` after confirming scheduling works
- [ ] Consolidate sluggable packages
- [ ] Review and remove unused middleware
- [ ] Delete upgrade scripts in `scripts/`

---

## 📊 Disk Space Summary

| Action | Space Saved |
|--------|-------------|
| Delete `old/` folder | ~724 MB |
| Delete `storage/tenant_*/` | ~1 MB |
| Removed packages (after composer update) | ~50 MB |
| **Total Potential Savings** | **~775 MB** |

---

## Notes

1. **PDF Generation:** Switched from DomPDF to mPDF for proper Arabic RTL support
2. **Fonts:** Using Tajawal font for Arabic text in PDFs
3. **Local Package:** `tasawk/api` now loads from `packages/` directory via path repository
4. **Tenancy:** Completely removed - was never used but was installed


