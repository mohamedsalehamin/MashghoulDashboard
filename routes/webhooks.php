<?php

use App\CatalogModule\Models\Transaction;
use App\ContentModule\Models\Category;
use App\DefaultPanel\Actions\AddPointToCustomerAction;
use App\DefaultPanel\Actions\AddReservationCommissionAction;
use App\DefaultPanel\Actions\OrderPaidAction;
use App\DefaultPanel\Actions\PayTransactionViaWallet;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Mail\SendEmailNotification;
use App\Notifications\ReservationCreatedSuccessfullyNotification;
use Illuminate\Support\Facades\Route;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

Route::get('testoo', function () {
    Mail::to("ahmed.mostafa.dev.eg@gmail.com")->send(new SendEmailNotification("As", "ASd"));
    dd('');
    $reservation = \App\CatalogModule\Models\Reservation::latest()->first();
    AddReservationCommissionAction::run($reservation);
    dd('as');
    $customer = \App\Models\User::find(13);
    $customer->notify(new \App\Notifications\WiningGiftSuccessfullyNotification([
        'ar' => __("panel.messages.your_are_gain_points_for_register", ['points' => GeneralSettings::getPointsOnAction('register')], 'ar'),
        'en' => __("panel.messages.your_are_gain_points_for_register", ['points' => GeneralSettings::getPointsOnAction('register')], 'en'),
    ]));

});

Route::get('webhooks/myfatoorah/transactions/callback', function (PaymentMyfatoorahApiV2 $myfatoorahApiV2) {
    $response = $myfatoorahApiV2->getPaymentStatus(request()->get('Id'), 'PaymentId');
    $transaction = Transaction::where('meta_data->invoiceId', $response->InvoiceId)->first();
    if ($response->InvoiceStatus == 'Paid') {
        if ($transaction->status != ReservationPaymentStatus::PAID) {
            $transaction->update([
                'status' => ReservationPaymentStatus::PAID,
                'meta_data' => array_merge($transaction->meta_data, [...collect($response)->toArray(), 'method' => $response->focusTransaction->PaymentGateway, 'paid_at' => now()]),
            ]);

            OrderPaidAction::run($transaction->transactionable);


        }
        session()->flash('reservation_id', $transaction->transactionable->id);
        return redirect()->route('checkout.success');
    }
    return redirect()->route('checkout.fail');
})->name('webhooks.myfatoorah.transactions.callback');
Route::get('categories/arrange', function () {
    foreach (request()->get("list") as $record) {
        Category::find($record['id'])->update(['parent_id' => $record['parent'] ?? null]);
    }
})->name('cp.categories.arrange');
