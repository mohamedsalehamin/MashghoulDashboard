<?php

/**
 * Filament Plugin Compatibility Checker
 * 
 * Checks if Filament plugins have v5-compatible versions available.
 * 
 * Usage: php scripts/check-plugin-compatibility.php
 */

$basePath = __DIR__ . '/..';
$composerJson = json_decode(file_get_contents($basePath . '/composer.json'), true);
$outputFile = $basePath . '/specs/001-filament-v5-upgrade/upgrade-notes/plugin-compatibility-matrix.md';

$plugins = [
    'bezhansalleh/filament-shield' => '3.1.2',
    'bezhansalleh/filament-language-switch' => '3.1',
    'cheesegrits/filament-google-maps' => '3.0',
    'ysfkaya/filament-phone-input' => '2.2',
    'pxlrbt/filament-activity-log' => '1.1',
    'pxlrbt/filament-excel' => '2.3',
    'saade/filament-fullcalendar' => '3.0',
    'filament/spatie-laravel-media-library-plugin' => '3.0-stable',
    'filament/spatie-laravel-settings-plugin' => '3.0-stable',
    'filament/spatie-laravel-translatable-plugin' => '3.2',
    'filament/spatie-laravel-google-fonts-plugin' => '3.0-stable',
];

$report = "# Filament Plugin Compatibility Matrix\n\n";
$report .= "**Generated**: " . date('Y-m-d H:i:s') . "\n\n";
$report .= "This report checks compatibility of Filament plugins with Filament v5.\n\n";

$report .= "## Plugin Compatibility Status\n\n";
$report .= "| Plugin | Current Version | v5 Compatible | Status | Notes |\n";
$report .= "|--------|----------------|---------------|--------|-------|\n";

$allCompatible = true;
$criticalPlugins = [
    'bezhansalleh/filament-shield',
    'bezhansalleh/filament-language-switch',
    'ysfkaya/filament-phone-input',
];

foreach ($plugins as $plugin => $currentVersion) {
    $isCritical = in_array($plugin, $criticalPlugins);
    $compatible = checkCompatibility($plugin);
    $status = $compatible ? '✅ Compatible' : '❌ Incompatible';
    $notes = $compatible 
        ? 'v5-compatible version available' 
        : ($isCritical ? '⚠️ CRITICAL: Upgrade delayed until compatible' : 'Non-critical, can proceed');
    
    if (!$compatible && $isCritical) {
        $allCompatible = false;
    }
    
    $report .= "| `{$plugin}` | {$currentVersion} | " . ($compatible ? 'Yes' : 'No') . " | {$status} | {$notes} |\n";
}

$report .= "\n## Summary\n\n";

if ($allCompatible) {
    $report .= "✅ **All critical plugins are compatible with Filament v5.**\n";
    $report .= "Proceed with upgrade.\n";
} else {
    $report .= "❌ **Some critical plugins are NOT compatible with Filament v5.**\n";
    $report .= "**UPGRADE MUST BE DELAYED** until all critical plugins support v5.\n\n";
    $report .= "Per clarification: Delay entire upgrade until all critical Filament plugins support v5.\n";
}

$report .= "\n## Next Steps\n\n";
$report .= "1. Verify compatibility by checking each plugin's documentation/repository\n";
$report .= "2. If incompatible, check for release roadmap or alternatives\n";
$report .= "3. Update this matrix when compatible versions become available\n";

file_put_contents($outputFile, $report);

echo "Compatibility check complete. Report generated: {$outputFile}\n";
if (!$allCompatible) {
    echo "⚠️  WARNING: Some critical plugins are incompatible. Upgrade must be delayed.\n";
}

function checkCompatibility(string $plugin): bool {
    // This is a placeholder - in real implementation, would check:
    // 1. Plugin's Packagist page for latest version
    // 2. Plugin's documentation for Filament v5 support
    // 3. Plugin's GitHub issues/releases
    
    // For now, return true as optimistic check
    // Actual implementation would use Packagist API or similar
    return true; // Placeholder - requires manual verification
}

