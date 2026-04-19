<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Multi-domain / subdomain panels (e.g. portal.* vs main APP_URL) must generate
 * storage URLs on the same host as the current request; otherwise JS (Filament,
 * Livewire, fetch) hits another origin and the browser blocks with CORS.
 */
class AlignPublicDiskUrlWithRequestHost
{
    public function handle(Request $request, Closure $next): Response
    {
        config([
            'filesystems.disks.public.url' => $request->getSchemeAndHttpHost().'/storage',
        ]);

        return $next($request);
    }
}
