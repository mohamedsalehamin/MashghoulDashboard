<?php
Route::get('reservations/{reservation}/invoice',function (\App\CatalogModule\Models\Reservation $reservation){
//    return pdf()
//        ->view('site.pages.invoice', compact('reservation'))
//        ->name('invoice-2023-04-10.pdf')
//        ->download();



    $pdf = PDF::loadView('site.pages.invoice',['reservation' => $reservation]);
    $suffix="{$reservation->id}_".date("Y_m_d");
    return $pdf->download("invoice_$suffix.pdf");
})->name('reservations.invoice');
