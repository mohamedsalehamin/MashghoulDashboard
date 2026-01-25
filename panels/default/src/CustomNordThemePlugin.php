<?php

namespace App\DefaultPanel;

use Andreia\FilamentNordTheme\FilamentNordThemePlugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Vite;

class CustomNordThemePlugin extends FilamentNordThemePlugin
{
    public function register(Panel $panel): void
    {
        // Register vite theme first (from parent)
        $panel->viteTheme('vendor/andreia/filament-nord-theme/resources/css/theme.css');

        // Register custom sunset theme CSS override (built via Vite)
        // Use Vite to get the correct asset path with hash
        $cssPath = Vite::asset('resources/css/filament/admin/custom-sunset-theme.css');
        $panel->assets([
            \Filament\Support\Assets\Css::make('custom-sunset-theme', $cssPath),
        ]);

        // Set custom colors matching sunset theme
        $panel->colors([
            'danger' => Color::hex('#ef4444'), // Red
            'gray' => [
                50 => '#f9fafb',
                100 => '#f3f4f6',
                200 => '#e5e7eb',
                300 => '#d1d5db',
                400 => '#9ca3af',
                500 => '#6b7280',
                600 => '#4b5563',
                700 => '#374151',
                800 => '#1f2937',
                900 => '#111827',
                950 => '#030712',
            ],
            'info' => Color::hex('#3b82f6'), // Blue
            'primary' => [
                50 => '#FFF7ED',  // rgb(255, 247, 237)
                100 => '#FFEDD5', // rgb(255, 237, 213)
                200 => '#FED7AA', // rgb(254, 215, 170)
                300 => '#FDB874', // rgb(253, 186, 116)
                400 => '#FB923C', // rgb(251, 146, 60)
                500 => '#F97316', // rgb(249, 115, 22) - Sunset Orange
                600 => '#EA580C', // rgb(234, 88, 12)
                700 => '#C2410C', // rgb(194, 65, 12)
                800 => '#9A3412', // rgb(154, 52, 18)
                900 => '#7C2D12', // rgb(124, 45, 18)
                950 => '#431407', // rgb(67, 20, 7)
            ],
            'secondary' => Color::hex('#5e81ac'), // Blue
            'success' => Color::hex('#22c55e'), // Green
            'warning' => Color::hex('#f59e0b'), // Yellow/Orange
        ]);
    }
}

