<?php

namespace App\Http\Middleware;

use Api;
use App\DefaultPanel\Enum\ReservationStatus;
use Closure;
use Illuminate\Http\Request;
use App\DefaultPanel\Enum\OrderStatus;

class EnsureThatReservationDeliveredMiddleware {

    public function handle(Request $request, Closure $next) {
        if ($request->route('reservation')->status->value != ReservationStatus::COMPLETED->value) {
            return Api::isError(__('validation.api.order_not_delivered_yet'));
        }
        return $next($request);
    }
}
