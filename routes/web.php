<?php

use Mcamara\LaravelLocalization\Facades\LaravelLocalization as GlobalLaravelLocalization;
use App\Http\Controllers\SiteController;
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

Route::get('/checkout/success', fn() => 'success')->name('checkout.success');
Route::get('/checkout/fail', fn() => 'fail')->name('checkout.fail');

// Localized routes with catch-all page route at the end
Route::group([
    'prefix' => GlobalLaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    Route::get('/', [SiteController::class, 'index'])->name('site.home');
    Route::get('/register', [SiteController::class, 'register'])->name('site.pages.register');
    Route::get('s/{provider_slug}', [SiteController::class, 'share_provider'])->name('site.share_provider');
    // Keep the catch-all /{page} route LAST
    Route::get('/{page}', [SiteController::class, 'page'])->name('site.page');
});
