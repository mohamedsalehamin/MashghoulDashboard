<?php

namespace App\Http\Middleware;

use Api;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureThatReservationNotCanceledBeforeMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next) {
        if ($request->route('reservation')->cancellation()->exists()) {
            return Api::isError(__('validation.api.reservation_already_reported'));

        }
        return $next($request);
    }
}
