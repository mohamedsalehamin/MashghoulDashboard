<?php

use App\CatalogModule\Models\Transaction;
use App\ContentModule\Models\Category;
use App\DefaultPanel\Actions\AddPointToCustomerAction;
use App\DefaultPanel\Actions\AddReservationCommissionAction;
use App\DefaultPanel\Actions\PayTransactionViaWallet;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Mail\SendEmailNotification;
use App\Notifications\ReservationCreatedSuccessfullyNotification;
use Illuminate\Support\Facades\Route;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

Route::get('testoo', function () {
    Mail::to("ahmed.mostafa.dev.eg@gmail.com")->send( new SendEmailNotification("As", "ASd"));
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

            if ($transaction->transactionable_type == \App\CatalogModule\Models\Reservation::class) {
                $reservation = $transaction->transactionable;
                $reservation->addTimeline([
                    'ar' => __('panel.messages.reservation_created_successfully', [], 'ar'),
                    'en' => __('panel.messages.reservation_created_successfully', [], 'en')
                ], 'created');

                $description = [
                    'ar' => __("panel.messages.gift_for_reservation", ['id' => $reservation->id], 'ar'),
                    'en' => __("panel.messages.gift_for_reservation", ['id' => $reservation->id], 'en'),
                ];
                AddPointToCustomerAction::run($reservation->customer, GeneralSettings::getPointsOnAction('reserve'), ['description' => $description]);
//                $reservation->patient->notify(new ReservationCreatedSuccessfullyNotification($reservation));

                Notification::send([...\App\DefaultPanel\Lib\Utils::getAdministrationUsers(), $reservation->reservable->user], new ReservationCreatedSuccessfullyNotification($reservation));
                $reservation->customer->notify(new \App\Notifications\WiningGiftSuccessfullyNotification([
                    'ar' => __("panel.messages.you_are_gain_points_for_reservation", ['points' => GeneralSettings::getPointsOnAction('reserve'), 'id' => $reservation->id], 'ar'),
                    'en' => __("panel.messages.you_are_gain_points_for_reservation", ['points' => GeneralSettings::getPointsOnAction('reserve'), 'id' => $reservation->id], 'en'),
                ]));
            }


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
