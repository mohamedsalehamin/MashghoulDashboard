# Security Advisories Report

**Generated**: 2026-01-22
**Status**: Manual verification required

## Overview

This document tracks security advisories and known vulnerabilities for packages in the system.

## Security-Critical Packages

**Definition** (per clarification): Packages with active security advisories OR known vulnerabilities.

## Check Method

Run the following command to check for security advisories:

```bash
composer audit
```

Or check individual packages at:
- https://packagist.org/packages/[vendor]/[package]
- https://github.com/advisories (GitHub Security Advisories)

## Packages to Check

### Filament Packages
- filament/filament (current: ^3.2, target: ^5.0)
- filament/tables (current: ^3.2, target: ^5.0)
- filament/infolists (current: ^3.2, target: ^5.0)

### Filament Plugins
- bezhansalleh/filament-shield
- bezhansalleh/filament-language-switch
- cheesegrits/filament-google-maps
- ysfkaya/filament-phone-input
- pxlrbt/filament-activity-log
- pxlrbt/filament-excel
- saade/filament-fullcalendar
- filament/spatie-laravel-media-library-plugin
- filament/spatie-laravel-settings-plugin
- filament/spatie-laravel-translatable-plugin
- filament/spatie-laravel-google-fonts-plugin

### Core Dependencies
- laravel/framework
- livewire/livewire
- stancl/tenancy

### Other Packages
- All packages listed in composer.json

## Security Advisory Results

**Status**: ✅ Audit completed

**Found**: 8 security vulnerability advisories affecting 7 packages

**Action Required**:
1. ✅ Run `composer audit` - Completed
2. ⏳ Identify which packages are security-critical (advisories OR vulnerabilities)
3. ⏳ Mark those packages for priority upgrade
4. ⏳ Upgrade security-critical packages first

## Security-Critical Packages Identified

**Note**: Full audit output shows vulnerabilities in:
- league/commonmark (XSS vulnerability in Attributes extension)
- libsodium (vulnerability - see https://00f.net/2025/12/30/libsodium-vulnerability)
- PHPOffice/PhpSpreadsheet (see GitHub security advisory)

**Action**: These packages should be upgraded first if they are in the dependency tree and have security patches available.

| Package | Advisory/Vulnerability | Priority | Action |
|---------|------------------------|----------|--------|
| laravel/framework | CVE-2025-27515 (File Validation Bypass, Medium) | High | Upgrade to patched version (check Laravel 10.x latest) |
| league/commonmark | XSS vulnerability in Attributes extension | High | Upgrade to patched version (currently 2.6.1) |
| phpoffice/phpspreadsheet | Security advisory (GitHub) | High | Upgrade to patched version (currently 1.29.10) |
| libsodium | Vulnerability (see URL) | High | Check if dependency, upgrade if needed |

## Notes

- Security-critical packages must be upgraded first (per FR-002)
- Upgrade priority: Security patches > Major version upgrades
- All security-critical packages should have upgrade_priority <= 2

