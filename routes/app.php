<?php

use Mcamara\LaravelLocalization\Facades\LaravelLocalization as GlobalLaravelLocalization;

use App\Http\Controllers\SiteController;

Route::group(['prefix' => GlobalLaravelLocalization::setLocale()], function () {
    // Your other localized routes...

    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/livewire/update', $handle);
    });
});
Route::group([
    'prefix' => GlobalLaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    Route::view('/', 'site.pages.index')->name('site.home');
    Route::view('/register', 'site.pages.register')->name('site.pages.register');
    Route::get('/{page}', [SiteController::class, 'page'])->name('site.page');
});
Route::get('reservations/{reservation}/invoice', function (\App\CatalogModule\Models\Reservation $reservation) {
//    return pdf()
//        ->view('site.pages.invoice', compact('reservation'))
//        ->name('invoice-2023-04-10.pdf')
//        ->download();


    $pdf = PDF::loadView('site.pages.invoice', ['reservation' => $reservation]);
    $suffix = "{$reservation->id}_" . date("Y_m_d");
    return $pdf->download("invoice_$suffix.pdf");
})->name('reservations.invoice');

Route::get('/checkout/success', fn() => 'success')->name('checkout.success');
Route::get('/checkout/fail', fn() => 'fail')->name('checkout.fail');
