<?php

namespace App\Http\Middleware;

use Api;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureThatReservationNotRatedBeforeMiddleware {

    public function handle(Request $request, Closure $next) {
        if ($request->route('reservation')->rate()->exists()) {
            return Api::isError(__('validation.api.reservation_already_rated'));
        }
        return $next($request);
    }
}
