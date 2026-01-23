# Laravel 12 Upgrade Notes

**Date**: 2026-01-22  
**Status**: In Progress

## Packages Updated in composer.json

### Core Laravel Packages
- ✅ `laravel/framework`: `^10.1.0` → `^12.0`
- ✅ `laravel/sanctum`: `^3.2` → `^4.0`
- ✅ `laravel/tinker`: `^2.8` → `^2.9`
- ✅ `php`: `^8.3` → `^8.2` (Laravel 12 requires PHP 8.2+)

### Related Packages
- ✅ `matanyadaev/laravel-eloquent-spatial`: `^3.2` → `^4.0`
- ✅ `mcamara/laravel-localization`: `^2.2` → `^2.3`
- ✅ `wire-elements/modal`: `^2.0` → `^3.0`
- ✅ `cknow/laravel-money`: `^7.2` → `^8.0`
- ✅ `tightenco/ziggy`: `^1.6` → `^2.0`

### Dev Dependencies
- ✅ `nunomaduro/collision`: `^7.0` → `^8.0`
- ✅ `phpunit/phpunit`: `^10.1` → `^11.0`
- ✅ `spatie/laravel-ignition`: `^2.0` → `^2.4`

### Packages Kept at Current Version (Compatibility Issues)
- ⚠️ `livewire/livewire`: Kept at `^3.4` (Filament v3 requires Livewire v3)
- ⚠️ `pxlrbt/filament-excel`: Kept at `^2.4` (v3.x requires Filament v4+)

## Known Compatibility Issues

### Blockers
1. **pxlrbt/filament-excel v2.4.1** has dependency `anourvalar/eloquent-serialize ^1.2` which only supports Laravel up to v11
   - **Current Status**: Blocking Laravel 12 upgrade
   - **Solution Options**:
     a. Temporarily remove `pxlrbt/filament-excel` to allow upgrade, then find alternative
     b. Wait for `anourvalar/eloquent-serialize` to support Laravel 12
     c. Upgrade to Filament v5 first, then use `pxlrbt/filament-excel v3.x` (which may have different dependencies)
   - **Recommended**: Option (c) - proceed with Filament v5 upgrade as originally planned

2. **Filament v3** requires Livewire v3, preventing Livewire v4 upgrade
   - **Solution**: Upgrade to Filament v5 (which supports Livewire v4) as originally planned

## Next Steps

1. **Option A**: Temporarily remove `pxlrbt/filament-excel` to proceed with Laravel 12 upgrade
2. **Option B**: Proceed with Filament v5 upgrade first (original plan), then upgrade Laravel 12
3. **Option C**: Wait for package updates

## Recommended Approach

Since the original plan was to upgrade to Filament v5, and Filament v5 supports Laravel 12, the recommended approach is:

1. Complete Filament v5 upgrade (original plan)
2. Then upgrade Laravel 12 (which should resolve the `pxlrbt/filament-excel` issue via v3.x)
3. Upgrade Livewire to v4 (supported by Filament v5)

## Breaking Changes to Watch For

- Laravel 12 may have breaking changes from Laravel 10
- Check Laravel 12 upgrade guide: https://laravel.com/docs/12.x/upgrade
- Test all Filament panels and modules
- Verify multi-tenancy (stancl/tenancy) compatibility
- Test Excel export functionality if `pxlrbt/filament-excel` is temporarily removed
