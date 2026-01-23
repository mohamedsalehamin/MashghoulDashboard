<?php

/**
 * Filament Component Audit Script
 * 
 * Scans the codebase for custom Filament components and documents them
 * for Filament v5 upgrade compatibility assessment.
 * 
 * Usage: php scripts/audit-filament-components.php
 */

$basePath = __DIR__ . '/..';
$outputFile = $basePath . '/specs/001-filament-v5-upgrade/upgrade-notes/component-audit.md';

$components = [
    'resources' => [],
    'pages' => [],
    'widgets' => [],
    'form-components' => []
];

// Scan default panel
$defaultPanelPath = $basePath . '/panels/default/src';
scanDirectory($defaultPanelPath, 'default', $components);

// Scan providers panel
$providersPanelPath = $basePath . '/panels/providers/src';
scanDirectory($providersPanelPath, 'providers', $components);

// Generate audit report
generateReport($components, $outputFile);

echo "Audit complete. Report generated: {$outputFile}\n";

function scanDirectory(string $path, string $panel, array &$components): void {
    if (!is_dir($path)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            $relativePath = str_replace(__DIR__ . '/../', '', $file->getPathname());

            // Detect component type
            if (preg_match('/class\s+(\w+)\s+extends\s+.*Resource/', $content, $matches)) {
                $components['resources'][] = [
                    'panel' => $panel,
                    'class' => $matches[1],
                    'file' => $relativePath,
                    'type' => 'resource'
                ];
            } elseif (preg_match('/class\s+(\w+)\s+extends\s+.*Page/', $content, $matches)) {
                $components['pages'][] = [
                    'panel' => $panel,
                    'class' => $matches[1],
                    'file' => $relativePath,
                    'type' => 'page'
                ];
            } elseif (preg_match('/class\s+(\w+)\s+extends\s+.*Widget/', $content, $matches)) {
                $components['widgets'][] = [
                    'panel' => $panel,
                    'class' => $matches[1],
                    'file' => $relativePath,
                    'type' => 'widget'
                ];
            } elseif (preg_match('/class\s+(\w+)\s+extends\s+.*Component/', $content, $matches) && 
                      strpos($relativePath, 'Forms/Components') !== false) {
                $components['form-components'][] = [
                    'panel' => $panel,
                    'class' => $matches[1],
                    'file' => $relativePath,
                    'type' => 'form-component'
                ];
            }
        }
    }
}

function generateReport(array $components, string $outputFile): void {
    $report = "# Filament Component Audit Report\n\n";
    $report .= "**Generated**: " . date('Y-m-d H:i:s') . "\n\n";
    $report .= "This report documents all custom Filament components that may require updates for Filament v5 compatibility.\n\n";

    $total = count($components['resources']) + count($components['pages']) + 
             count($components['widgets']) + count($components['form-components']);
    
    $report .= "## Summary\n\n";
    $report .= "- **Total Components**: {$total}\n";
    $report .= "- **Resources**: " . count($components['resources']) . "\n";
    $report .= "- **Pages**: " . count($components['pages']) . "\n";
    $report .= "- **Widgets**: " . count($components['widgets']) . "\n";
    $report .= "- **Form Components**: " . count($components['form-components']) . "\n\n";

    // Resources
    if (!empty($components['resources'])) {
        $report .= "## Resources\n\n";
        foreach ($components['resources'] as $component) {
            $report .= "### {$component['class']}\n";
            $report .= "- **Panel**: {$component['panel']}\n";
            $report .= "- **File**: `{$component['file']}`\n";
            $report .= "- **Status**: ⏳ Pending v5 compatibility check\n\n";
        }
    }

    // Pages
    if (!empty($components['pages'])) {
        $report .= "## Pages\n\n";
        foreach ($components['pages'] as $component) {
            $report .= "### {$component['class']}\n";
            $report .= "- **Panel**: {$component['panel']}\n";
            $report .= "- **File**: `{$component['file']}`\n";
            $report .= "- **Status**: ⏳ Pending v5 compatibility check\n\n";
        }
    }

    // Widgets
    if (!empty($components['widgets'])) {
        $report .= "## Widgets\n\n";
        foreach ($components['widgets'] as $component) {
            $report .= "### {$component['class']}\n";
            $report .= "- **Panel**: {$component['panel']}\n";
            $report .= "- **File**: `{$component['file']}`\n";
            $report .= "- **Status**: ⏳ Pending v5 compatibility check\n\n";
        }
    }

    // Form Components
    if (!empty($components['form-components'])) {
        $report .= "## Form Components\n\n";
        foreach ($components['form-components'] as $component) {
            $report .= "### {$component['class']}\n";
            $report .= "- **Panel**: {$component['panel']}\n";
            $report .= "- **File**: `{$component['file']}`\n";
            $report .= "- **Status**: ⏳ Pending v5 compatibility check\n\n";
        }
    }

    $report .= "## Next Steps\n\n";
    $report .= "1. Review each component for Filament v3 API usage\n";
    $report .= "2. Identify v5 API equivalents\n";
    $report .= "3. Update components to use v5 API\n";
    $report .= "4. Test each component after update\n";

    file_put_contents($outputFile, $report);
}

