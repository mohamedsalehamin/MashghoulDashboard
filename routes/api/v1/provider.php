<?php


use App\DefaultPanel\Api\V1\Provider\AuthServices;
use App\DefaultPanel\Api\V1\Provider\ProfileServices;
use App\DefaultPanel\Api\V1\Provider\ReservationServices;

Route::prefix('v1/providers')->group(function () {

    Route::post('auth/login', [AuthServices::class, 'login']);
    Route::post('auth/register', [AuthServices::class, 'register']);

    Route::post('auth/password/forget', [AuthServices::class, 'forgetPassword']);
    Route::post('auth/password/reset', [AuthServices::class, 'resetPassword']);
    Route::post('auth/otp/verify', [AuthServices::class, 'verifySMSCode']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('profile', [ProfileServices::class, 'index']);
        Route::get('profile/rates', [ProfileServices::class, 'rates']);
        Route::put('profile/password', [ProfileServices::class, 'updatePassword']);
        Route::get('profile/reservations', [ReservationServices::class, 'index']);
        Route::get('profile/reservations/{reservation}', [ReservationServices::class, 'show']);
        Route::put('profile/reservations/{reservation}/status', [ReservationServices::class, 'changeStatus']);
        Route::get('statistics', [ReservationServices::class, 'statistics']);

    });

});
