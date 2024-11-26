<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\APIException;

class EnsureThatCustomerActive {
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws APIException
     */
    public function handle(Request $request, Closure $next): Response {
        if (!auth()->user()->active) {
            throw  new APIException(__('validation.api.account_suspend'));
        }
        return $next($request);
    }
}
