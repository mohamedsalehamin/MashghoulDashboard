<?php

use App\CatalogModule\Models\Transaction;
use App\ContentModule\Models\Category;
use App\DefaultPanel\Enum\ReservationStatus;
use App\Notifications\ReservationCreatedSuccessfullyNotification;
use Illuminate\Support\Facades\Route;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;


Route::get('webhooks/myfatoorah/transactions/callback', function (PaymentMyfatoorahApiV2 $myfatoorahApiV2) {
    $response = $myfatoorahApiV2->getPaymentStatus(request()->get('Id'), 'PaymentId');
    $transaction = Transaction::where('meta_data->invoiceId', $response->InvoiceId)->first();
    if ($response->InvoiceStatus == 'Paid') {
        if ($transaction->payment_status != 'paid') {
            $transaction->update([
                'status' => 'paid',
                'meta_data' => array_merge($transaction->meta_data, [...collect($response)->toArray(), 'method' => $response->focusTransaction->PaymentGateway, 'paid_at' => now()]),
            ]);
            if ($transaction->transactionable_type == \App\CatalogModule\Models\Subscription::class) {
                $transaction->subscription->update([
                    'status' => ReservationStatus::PROCESSING,
                    'start_date' => now(),
                    'end_date' => now()->addMonth()
                ]);
            }
            if ($transaction->transactionable_type == \App\CatalogModule\Models\Reservation::class) {
                $reservation = $transaction->transactionable;
                $reservation->addTimeline([
                    'ar' => __('panel.messages.reservation_created_successfully', [], 'ar'),
                    'en' => __('panel.messages.reservation_created_successfully', [], 'en')
                ], 'created');
//                $reservation->patient->notify(new ReservationCreatedSuccessfullyNotification($reservation));
                $reservation->reservable->user->notify(new ReservationCreatedSuccessfullyNotification($reservation));
            }


        }
        session()->flash('reservation_id', $transaction->transactionable->id);
        return 'success';
    }
    return redirect()->route('checkout.error');
})->name('webhooks.myfatoorah.transactions.callback');
Route::get('categories/arrange', function () {
    foreach (request()->get("list") as $record) {
        Category::find($record['id'])->update(['parent_id' => $record['parent'] ?? null]);
    }
})->name('cp.categories.arrange');
