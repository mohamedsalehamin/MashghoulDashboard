<?php

use App\DefaultPanel\Api\V1\Content\ArticleServices;
use App\DefaultPanel\Api\V1\Content\BannerServices;
use App\DefaultPanel\Api\V1\Content\ContentServices;
use App\DefaultPanel\Api\V1\DoctorServices;
use App\DefaultPanel\Api\V1\LabServices;
use App\DefaultPanel\Api\V1\LocationServices;
use App\DefaultPanel\Api\V1\NotificationServices;
use App\DefaultPanel\Api\V1\ProductServices;
use App\DefaultPanel\Api\V1\SettingServices;
use App\DefaultPanel\Api\V1\SharedProfileService;
use App\DefaultPanel\Api\V1\SpecializationServices;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('settings/contacts/types', [SettingServices::class, 'contactTypes']);
    Route::get('settings', [SettingServices::class, 'all']);
    Route::get('settings/reservation-statuses', [SettingServices::class, 'reservationStatuses']);
    Route::get('settings/cancellation-reasons', [SettingServices::class, 'cancellationReasons']);
    Route::get('settings/report-reasons', [SettingServices::class, 'reportReasons']);

    Route::get('banners', [BannerServices::class, 'list']);
    Route::get('search', [ProductServices::class, 'search']);
    Route::get('specializations', [SpecializationServices::class, 'list']);
    Route::get('specializations/{specialization}', [SpecializationServices::class, 'show']);

    Route::get('doctors', [DoctorServices::class, 'index']);
    Route::get('doctors/{doctor}', [DoctorServices::class, 'show']);
    Route::post('doctors/{doctor}/toggle-favorite', [DoctorServices::class, 'toggleFavorite'])->middleware('auth:sanctum');
    Route::post('doctors/{doctor}/available-times', [DoctorServices::class, 'availableTimes'])->middleware('auth:sanctum');
    Route::post('doctors/{doctor}/details', [DoctorServices::class, 'appointmentDetails'])->middleware('auth:sanctum');
    Route::post('doctors/{doctor}/reserve', [DoctorServices::class, 'reserve'])->middleware('auth:sanctum');

    Route::get('labs', [LabServices::class, 'index']);
    Route::get('labs/{lab}', [LabServices::class, 'show']);
    Route::post('labs/{lab}/toggle-favorite', [LabServices::class, 'toggleFavorite'])->middleware('auth:sanctum');
    Route::post('labs/{lab}/available-times', [LabServices::class, 'availableTimes'])->middleware('auth:sanctum');
    Route::post('labs/{lab}/details', [LabServices::class, 'appointmentDetails'])->middleware('auth:sanctum');
    Route::post('labs/{lab}/reserve', [LabServices::class, 'reserve'])->middleware('auth:sanctum');


    Route::get('chronic-diseases', [ContentServices::class, 'chronicDiseases']);
    Route::post('contacts', [ContentServices::class, 'contact']);
    Route::post('join', [ContentServices::class, 'join']);
    Route::get('pages/{slug}', [ContentServices::class, 'page']);
    Route::get('faqs', [ContentServices::class, 'faqs']);
    Route::get('customers-reviews', [ContentServices::class, 'customersReviews']);
    Route::get('articles', [ArticleServices::class, 'index']);
    Route::get('articles/categories', [ArticleServices::class, 'categories']);
    Route::get('articles/{article}', [ArticleServices::class, 'show']);

    Route::get('titles', [ContentServices::class, 'titles']);
    Route::get('locations/countries', [LocationServices::class, 'countries']);
    Route::get('locations/countries/{country}/states', [LocationServices::class, 'states']);
//    Route::get('locations/states', [LocationServices::class, 'states']);
    Route::get('locations/states/{state}/cities', [LocationServices::class, 'cities']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('users/device-token', [SharedProfileService::class, 'updateDeviceToken']);
        Route::delete('users/delete-account', [SharedProfileService::class, 'deleteAccount']);

        Route::get('users/notifications', [NotificationServices::class, 'all']);
        Route::delete('users/notifications/{id?}', [NotificationServices::class, 'destroy']);
        Route::post('users/notifications/fcm', [NotificationServices::class, 'fcm']);
    });

});





require_once __DIR__ . '/api/v1/index.php';



