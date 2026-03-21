<?php

use Livewire\Livewire;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as GlobalLaravelLocalization;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\ContentController;
use App\Http\Controllers\Site\CategoryController;
use App\Http\Controllers\Site\AuthController;
use App\Http\Controllers\Site\ProviderController;
use App\Http\Controllers\Site\BookingController;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

// Define specific routes BEFORE the catch-all route group
Route::get('reservations/{reservation}/invoice', function (\App\CatalogModule\Models\Reservation $reservation) {
    $suffix = "{$reservation->id}_" . date("Y_m_d");
    
    // Define custom font directory
    $fontDir = storage_path('fonts');
    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    
    // Create PDF with Arabic support and custom fonts
    $pdf = LaravelMpdf::loadView('site.pages.invoice', ['reservation' => $reservation], [], [
        'mode' => 'utf-8',
        'format' => 'A4',
        'autoScriptToLang' => true,
        'autoLangToFont' => true,
        'tempDir' => storage_path('app/mpdf'),
        'fontDir' => array_merge($defaultConfig['fontDir'], [$fontDir]),
        'fontdata' => $defaultFontConfig['fontdata'] + [
            'tajawal' => [
                'R' => 'Tajawal-Regular.ttf',
                'B' => 'Tajawal-Bold.ttf',
                'L' => 'Tajawal-Light.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ],
            'dinlight' => [
                'R' => 'din-light.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ],
            'dinbold' => [
                'R' => 'din-bold.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ],
        ],
        'default_font' => 'tajawal',
    ]);
    
    // Set RTL direction
    $pdf->getMpdf()->SetDirectionality('rtl');
    
    return $pdf->download("invoice_{$suffix}.pdf");
})->name('reservations.invoice');

/*
|--------------------------------------------------------------------------
| Livewire update route (must stay outside the locale prefix group)
|--------------------------------------------------------------------------
| If this is registered as /{locale}/livewire/update, the embedded URI in
| @livewireScripts must match exactly. Registering globally at /livewire/update
| avoids mismatches and failed wire:click requests on localized pages.
*/
Livewire::setUpdateRoute(function ($handle) {
    return \Illuminate\Support\Facades\Route::post('/livewire/update', $handle);
});

// Localized routes with catch-all page route at the end
Route::group([
    'prefix' => GlobalLaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'ensureLocationSet']
], function () {
    Route::get('/', [HomeController::class, 'index'])->name('site.home');

    Route::get('/faqs', [ContentController::class, 'faqs'])->name('site.faqs');
    Route::get('/contact', [ContentController::class, 'contact'])->name('site.contact');
    Route::get('/join', [ContentController::class, 'join'])->name('site.join');
    Route::get('/join/register', [AuthController::class, 'showJoinRegister'])->name('site.join.register');
    Route::get('/join/payment/failed', [ContentController::class, 'joinPaymentFailed'])->name('site.join.payment-failed');

    Route::get('/blog', [ContentController::class, 'blogs'])->name('site.blog');
    Route::get('/blog/{slug}', [ContentController::class, 'blogShow'])->name('site.blog.show');
    Route::get('/reviews', [ContentController::class, 'reviews'])->name('site.reviews');
    Route::post('/set-location', [ContentController::class, 'saveLocation'])->name('site.set-location.save');

    Route::get('/categories', [CategoryController::class, 'index'])->name('site.categories');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('site.category.show');
    Route::get('/providers/most-rated', [ProviderController::class, 'mostRated'])->name('site.providers.most-rated');
    Route::get('/providers/nearest', [ProviderController::class, 'nearest'])->name('site.providers.nearest');
    Route::get('/providers/{provider}', [ProviderController::class, 'show'])->name('site.provider.show');
    Route::get('/providers/{provider}/gallery', [ProviderController::class, 'gallery'])->name('site.provider.gallery');
    Route::get('/providers/{provider}/map', [ProviderController::class, 'map'])->name('site.provider.map');
    Route::post('/providers/{provider}/favorite/toggle', [ProviderController::class, 'toggleFavorite'])->name('site.provider.favorite.toggle')->middleware('auth:site');
    Route::post('/providers/{provider}/cart/add', [ProviderController::class, 'addToCart'])->name('site.provider.cart.add')->middleware('auth:site');

    Route::get('/booking/success', [BookingController::class, 'completedSuccess'])->name('site.booking.completed');
    Route::get('/booking/failed', [BookingController::class, 'completedFailed'])->name('site.booking.completed.failed');
    Route::get('/booking/error', [BookingController::class, 'checkoutError'])->name('site.booking.checkout.error');
    Route::get('/booking/{provider}', [BookingController::class, 'create'])->name('site.booking.create')->middleware('auth:site');
    Route::get('/reservations/{reservation}', [BookingController::class, 'show'])->name('site.booking.show')->middleware('auth:site');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('site.login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('site.register');
    Route::get('/register-success', [AuthController::class, 'showRegisterSuccess'])->name('site.register.success');
    Route::middleware('auth:site')->group(function () {
        Route::view('/account', 'site.new.account.info')->name('site.account.info');
        Route::view('/account/wallet', 'site.new.account.wallet')->name('site.account.wallet');
        Route::view('/account/notifications', 'site.new.account.notifications')->name('site.account.notifications');
        Route::view('/account/rewards', 'site.new.account.rewards')->name('site.account.rewards');
        Route::view('/my-reservations', 'site.new.account.bookings')->name('site.bookings');
        Route::view('/favorites', 'site.new.account.favorites')->name('site.favorites');
    });

    // Keep the catch-all /{page} route LAST (CMS pages)
    Route::get('/{page}', [ContentController::class, 'page'])->name('site.page');
});
