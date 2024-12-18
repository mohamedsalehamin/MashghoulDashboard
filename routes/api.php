<?php

use App\DefaultPanel\Api\V1\Customer\CartServices;
use App\DefaultPanel\Api\V1\Customer\CategoryServices;
use App\DefaultPanel\Api\V1\Customer\Content\BannerServices;
use App\DefaultPanel\Api\V1\Customer\Content\ContentServices;
use App\DefaultPanel\Api\V1\Customer\Content\SliderServices;
use App\DefaultPanel\Api\V1\Customer\LocationServices;
use App\DefaultPanel\Api\V1\Customer\NotificationServices;
use App\DefaultPanel\Api\V1\Customer\ProductServices;
use App\DefaultPanel\Api\V1\Customer\ProvidersServices;
use App\DefaultPanel\Api\V1\Customer\SettingServices;
use App\DefaultPanel\Api\V1\Customer\SharedProfileService;
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

require_once __DIR__ . '/api/v1/customer.php';
require_once __DIR__ . '/api/v1/provider.php';

Route::prefix('v1')->group(function () {
    Route::get('settings/contacts/types', [SettingServices::class, 'contactTypes']);
    Route::get('settings', [SettingServices::class, 'all']);
    Route::get('settings/reservation-statuses', [SettingServices::class, 'reservationStatuses']);

    Route::get('banners', [BannerServices::class, 'list']);
    Route::get('sliders', [SliderServices::class, 'list']);
    Route::get('plans', [ContentServices::class, 'points']);
    Route::get('categories', [CategoryServices::class, 'list']);
    Route::get('categories/{category}', [CategoryServices::class, 'show']);
    Route::post('/providers/{provider}/cart/details', [CartServices::class, 'details'])->middleware('auth:sanctum');
    Route::post('/providers/{provider}/cart/checkout', [CartServices::class, 'checkout'])->middleware('auth:sanctum');


    Route::get('providers', [ProvidersServices::class, 'index']);
    Route::get('providers/{provider}', [ProvidersServices::class, 'show']);
    Route::get('providers/{provider}/seats', [ProvidersServices::class, 'seats']);
    Route::get('providers/{provider}/seats/{seat}/times/available', [ProvidersServices::class, 'availableTimes']);
    Route::post('providers/{provider}/favorite/toggle', [ProvidersServices::class, 'toggleFavorite'])->middleware('auth:sanctum');

    Route::get('search', [ProductServices::class, 'search']);
    Route::post('contacts', [ContentServices::class, 'contact']);
    Route::get('contacts/types', [ContentServices::class, 'types']);
    Route::post('join', [ContentServices::class, 'join']);
    Route::get('pages/{slug}', [ContentServices::class, 'page']);
    Route::get('faqs', [ContentServices::class, 'faqs']);
    Route::get('customers-reviews', [ContentServices::class, 'customersReviews']);

    Route::get('titles', [ContentServices::class, 'titles']);
    Route::get('locations/countries', [LocationServices::class, 'countries']);
    Route::get('locations/countries/{country}/states', [LocationServices::class, 'states']);
//    Route::get('locations/states', [LocationServices::class, 'states']);
    Route::get('locations/states/{state}/cities', [LocationServices::class, 'cities']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('users/device-token', [SharedProfileService::class, 'updateDeviceToken']);
        Route::delete('users/delete-account', [SharedProfileService::class, 'deleteAccount']);

        Route::get('users/notifications', [NotificationServices::class, 'all']);
        Route::put('users/notifications/{notification}', [NotificationServices::class, 'seen']);
        Route::delete('users/notifications/{id?}', [NotificationServices::class, 'destroy']);
        Route::post('users/notifications/fcm', [NotificationServices::class, 'fcm']);
    });

});







