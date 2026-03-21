<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProviderHasActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $provider = \App\UsersModule\Models\Provider::where('user_id', $user->id)->first();
        if (!$provider) {
            return $next($request);
        }

        if (!$provider->hasActiveSubscription()) {
            // Allow access to plans + dashboard so provider can see subscription status and renew.
            if (
                $request->routeIs('filament.lab-panel.resources.plans.*') ||
                $request->routeIs('filament.lab-panel.pages.dashboard')
            ) {
                return $next($request);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => __('panel.notifications.provider_subscription_expired')], 403);
            }
            return redirect()->route('filament.lab-panel.resources.plans.index')
                ->with('error', __('panel.notifications.provider_subscription_expired'));
        }

        return $next($request);
    }
}
