<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureThatCartExist {
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response {
        if (!site()->reservation()->services()->count()) {
            return redirect()->route('checkout.error');
        }
        return $next($request);
    }
}
