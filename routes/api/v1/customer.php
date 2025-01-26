<?php


use App\DefaultPanel\Api\V1\Customer\AuthServices;
use App\DefaultPanel\Api\V1\Customer\Profile\FavoriteService;
use App\DefaultPanel\Api\V1\Customer\Profile\PointService;
use App\DefaultPanel\Api\V1\Customer\Profile\ProfileService;
use App\DefaultPanel\Api\V1\Customer\Profile\ReservationsServices;
use App\DefaultPanel\Api\V1\Customer\Profile\WalletService;
use App\Http\Middleware\EnsureThatReservationBelongToAuthUserMiddleware;
use App\Http\Middleware\EnsureThatReservationNotCanceledBeforeMiddleware;
use App\Http\Middleware\EnsureThatReservationNotRatedBeforeMiddleware;
use App\Http\Middleware\EnsureThatReservationNotReportedBeforeMiddleware;


Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthServices::class, 'login']);
    Route::post('auth/register', [AuthServices::class, 'register']);
    Route::post('auth/account/verify', [AuthServices::class, 'verify']);
    Route::post('auth/otp/verify', [AuthServices::class, 'verifySMSCode']);
    Route::post('auth/otp/send', [AuthServices::class, 'sendOTP']);
    Route::middleware(["auth:sanctum"])->group(function () {

        Route::get('profile', [ProfileService::class, 'index']);
        Route::get('profile/points', [PointService::class, 'index']);
        Route::get('profile/points/exchanges', [PointService::class, 'exchanges']);
        Route::get('profile/points/usages', [PointService::class, 'usages']);
        Route::post('profile/plans/{plan}/exchange', [PointService::class, 'exchange']);
        Route::get('profile/transactions', [ProfileService::class, 'transactions']);
        Route::get('profile/wallet/transactions', [WalletService::class, 'index']);
        Route::get('profile/favorites', [FavoriteService::class, 'index']);

        Route::post('profile', [ProfileService::class, 'update']);

        Route::post('profile/update-password', [ProfileService::class, 'updatePassword']);
        Route::get('profile/reservations', [ReservationsServices::class, 'index']);
        Route::get('profile/reservations/{reservation}', [ReservationsServices::class, 'show']);
        Route::post('profile/reservations/{reservation}/rate', [ReservationsServices::class, 'rate'])->middleware([
            EnsureThatReservationBelongToAuthUserMiddleware::class,
            EnsureThatReservationNotRatedBeforeMiddleware::class
        ]);
        Route::post('profile/settings', [ProfileService::class, 'settings']);

        Route::post('verify-alt-phone', [ProfileService::class, 'verifyAltPhone']);


    });
});
