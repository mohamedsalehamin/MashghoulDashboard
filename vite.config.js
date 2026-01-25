import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        react(),
        laravel({
            input: [
                'resources/css/app.css',
                'vendor/andreia/filament-nord-theme/resources/css/theme.css',
                'resources/css/filament/admin/custom-sunset-theme.css',
            ],
            refresh: true,
        }),
    ],
});
