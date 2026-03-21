<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocationSet
{
    /**
     * Routes that do not require location to be set.
     */
    protected array $except = [
        'site.set-location.save',
        'site.login',
        'site.register',
        'site.register.success',
        'site.blog',
        'site.blog.show',
        'site.faqs',
        'site.contact',
        'site.join',
        'site.join.register',
        'site.join.payment-failed',
        'livewire.update',
        'site.categories',
        'filament.admin*',
        'filament.lab-panel*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->locationAlreadySet($request)) {
            return $next($request);
        }

        if ($this->inExceptRoute($request)) {
            return $next($request);
        }

        if ($this->isPanelPath($request)) {
            return $next($request);
        }

        session(['intended_url' => $request->fullUrl()]);

        // Force the user to select a location, but keep them on the current page.
        // The frontend layout will render a non-closable modal when this flag exists.
        session(['show_location_modal' => true]);

        return $next($request);
    }

    protected function isPanelPath(Request $request): bool
    {
        $path = $request->path();

        // Admin panel: /admin or /en/admin, /ar/admin, etc.
        if (preg_match('#^(?:[a-z]{2}/)?admin(?:\b|/)#', $path) || $path === 'admin') {
            return true;
        }

        // Portal (lab-panel) is on a different domain; no path check needed for main domain
        return false;
    }

    protected function locationAlreadySet(Request $request): bool
    {
        return session()->has('location_set') && session()->has('user_latitude') && session()->has('user_longitude');
    }

    protected function inExceptRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if (! $routeName) {
            return false;
        }

        foreach ($this->except as $except) {
            if ($routeName === $except || (str_ends_with($except, '*') && str_starts_with($routeName, rtrim($except, '*')))) {
                return true;
            }
        }

        return false;
    }
}
