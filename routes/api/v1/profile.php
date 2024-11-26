<?php

use App\DefaultPanel\Api\V1\Profile\AddressBookService;
use App\DefaultPanel\Api\V1\Profile\ConsultingReservationsService;
use App\DefaultPanel\Api\V1\Profile\FavoriteService;
use App\DefaultPanel\Api\V1\Profile\MedicalTestsServices;
use App\DefaultPanel\Api\V1\Profile\ProfileService;
use App\DefaultPanel\Api\V1\Profile\ReservationsServices;
use App\Http\Middleware\EnsureThatReservationBelongToAuthUserMiddleware;
use App\Http\Middleware\EnsureThatReservationNotCanceledBeforeMiddleware;
use App\Http\Middleware\EnsureThatReservationNotRatedBeforeMiddleware;
use App\Http\Middleware\EnsureThatReservationNotReportedBeforeMiddleware;
use App\Http\Middleware\EnsureThatReservationNotScheduleBeforeMiddleware;

Route::middleware(["auth:sanctum"])->group(function () {

    Route::get('profile', [ProfileService::class, 'index']);
    Route::get('profile/health-data', [ProfileService::class, 'HealthData']);
    Route::delete('profile/analysis/{id}', [ProfileService::class, 'deleteAnalysisDelete']);
    Route::get('profile/transactions', [ProfileService::class, 'transactions']);
    Route::get('profile/favorites/doctors', [FavoriteService::class, 'doctors']);
    Route::get('profile/favorites/labs', [FavoriteService::class, 'labs']);

    Route::post('profile', [ProfileService::class, 'update']);

    Route::post('profile/update-password', [ProfileService::class, 'updatePassword']);
    Route::get('profile/my-consultations', [ConsultingReservationsService::class, 'index']);
    Route::get('profile/my-consultations/{consultation}', [ConsultingReservationsService::class, 'show']);
    Route::get('profile/my-consultations/{consultation}/prescription', [ConsultingReservationsService::class, 'prescription']);

    Route::get('profile/my-medical-tests', [MedicalTestsServices::class, 'index']);
    Route::get('profile/my-medical-tests/{reservation}', [MedicalTestsServices::class, 'show']);

    Route::post('profile/reservations/{reservation}/schedule', [ReservationsServices::class, 'schedule'])->middleware([
        EnsureThatReservationBelongToAuthUserMiddleware::class,
        EnsureThatReservationNotScheduleBeforeMiddleware::class
    ]);
    Route::post('profile/reservations/{reservation}/toggle-share/{analysis}', [ReservationsServices::class, 'toggleShare'])->middleware([

    ]);
    Route::post('profile/reservations/{reservation}/accept-schedule', [ReservationsServices::class, 'acceptScheduleReservationDate']);
    Route::post('profile/reservations/{reservation}/reject-schedule', [ReservationsServices::class, 'rejectScheduleReservationDate']);
    Route::post('profile/reservations/{reservation}/rate', [ReservationsServices::class, 'rate'])->middleware([
        EnsureThatReservationBelongToAuthUserMiddleware::class,
        EnsureThatReservationNotRatedBeforeMiddleware::class
    ]);
    Route::post('profile/reservations/{reservation}/session/join', [ReservationsServices::class, 'join'])->middleware([]);
    Route::post('profile/reservations/{reservation}/session/left', [ReservationsServices::class, 'left'])->middleware([]);
    Route::post('profile/reservations/{reservation}/session/end', [ReservationsServices::class, 'end'])->middleware([]);


    Route::post('profile/reservations/{reservation}/revisit', [ReservationsServices::class, 'revisit'])->middleware([
        EnsureThatReservationBelongToAuthUserMiddleware::class
    ]);
    Route::post('profile/reservations/{reservation}/confirm', [ReservationsServices::class, 'confirm'])->middleware([
        EnsureThatReservationBelongToAuthUserMiddleware::class
    ]);
//        ->middleware([EnsureThatReservationBelongToAuthUserMiddleware::class, EnsureThatReservationNotRatedBeforeMiddleware::class, EnsureThatReservationDeliveredMiddleware::class]);
    Route::post('profile/reservations/{reservation}/report', [ReservationsServices::class, 'report'])->middleware([
        EnsureThatReservationBelongToAuthUserMiddleware::class,
        EnsureThatReservationNotReportedBeforeMiddleware::class
    ]);
    Route::post('profile/reservations/{reservation}/cancel', [ReservationsServices::class, 'cancel'])->middleware([
        EnsureThatReservationBelongToAuthUserMiddleware::class,
        EnsureThatReservationNotCanceledBeforeMiddleware::class
    ]);




    Route::post('profile/settings', [ProfileService::class, 'settings']);

    Route::post('verify-alt-phone', [ProfileService::class, 'verifyAltPhone']);


});
