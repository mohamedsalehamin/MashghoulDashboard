<?php


use App\Http\Controllers\SiteController;

Route::view('/','site.pages.index')->name('site.home');
Route::get('/{page}',[SiteController::class,'page'])->name('site.page');
Route::get('reservations/{reservation}/invoice',function (\App\CatalogModule\Models\Reservation $reservation){
//    return pdf()
//        ->view('site.pages.invoice', compact('reservation'))
//        ->name('invoice-2023-04-10.pdf')
//        ->download();



    $pdf = PDF::loadView('site.pages.invoice',['reservation' => $reservation]);
    $suffix="{$reservation->id}_".date("Y_m_d");
    return $pdf->download("invoice_$suffix.pdf");
})->name('reservations.invoice');

Route::get('/checkout/success',fn()=>'success')->name('checkout.success');
Route::get('/checkout/fail',fn()=>'fail')->name('checkout.fail');
