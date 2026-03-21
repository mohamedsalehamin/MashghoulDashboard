<?php

use App\CatalogModule\Models\Subscription;
use App\CatalogModule\Models\Transaction;
use App\CatalogModule\Models\Reservation;
use App\ContentModule\Models\Category;
use App\DefaultPanel\Actions\CancelReservationOnPaymentFailureAction;
use App\DefaultPanel\Actions\OrderPaidAction;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\SubscriptionsStatusEnum;
use App\DefaultPanel\Enum\UserStatus;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use App\Http\Controllers\Webhook\TabbyController;



Route::any('webhooks/myfatoorah/transactions/callback', function (MyFatoorahPaymentStatus $myfatoorahApiV2) {
    $response = $myfatoorahApiV2->getPaymentStatus(request()->get('Id'), 'PaymentId');
    $transaction = Transaction::where('meta_data->invoiceId', $response->InvoiceId)->first();

    if (!$transaction) {
        return redirect()->route('site.booking.completed.failed');
    }

    if ($response->InvoiceStatus == 'Paid') {
        if ($transaction->status != ReservationPaymentStatus::PAID) {
            $transaction->update([
                'status' => ReservationPaymentStatus::PAID,
                'meta_data' => array_merge($transaction->meta_data ?? [], [...collect($response)->toArray(), 'method' => $response->focusTransaction->PaymentGateway, 'paid_at' => now()]),
            ]);

            $transactionable = $transaction->transactionable;

            if ($transactionable instanceof Reservation) {
                OrderPaidAction::run($transactionable);
                session()->flash('reservation_id', $transactionable->id);
                return redirect()->route('site.booking.completed');
            }

            if ($transactionable instanceof Subscription) {
                $transactionable->update(['status' => SubscriptionsStatusEnum::PROCESSING]);
                User::where('id', $transactionable->user_id)->update(['active' => UserStatus::ACTIVE]);
                $loginUrl = Filament::getPanel('lab-panel')->getLoginUrl();

                return redirect()->to($loginUrl . '?subscription=activated');
            }
        }

        // Already paid, redirect based on type
        $transactionable = $transaction->transactionable;
        if ($transactionable instanceof Reservation) {
            session()->flash('reservation_id', $transactionable->id);
            return redirect()->route('site.booking.completed');
        }
        if ($transactionable instanceof Subscription) {
            User::where('id', $transactionable->user_id)->update(['active' => UserStatus::ACTIVE]);
            $loginUrl = Filament::getPanel('lab-panel')->getLoginUrl();

            return redirect()->to($loginUrl . '?subscription=activated');
        }
        return redirect()->route('site.booking.completed');
    }

    // Payment failed: mark transaction canceled, cancel reservation to free slot
    $transaction->update(['status' => ReservationPaymentStatus::CANCELED->value]);
    if ($transaction->transactionable && $transaction->transactionable instanceof Reservation) {
        CancelReservationOnPaymentFailureAction::run($transaction->transactionable);
    }

    if ($transaction->transactionable instanceof Subscription) {
        return redirect()->route('site.join.payment-failed');
    }

    return redirect()->route('site.booking.completed.failed');
})->name('webhooks.myfatoorah.transactions.callback');
Route::get('categories/arrange', function () {
    foreach (request()->get("list") as $record) {
        Category::find($record['id'])->update(['parent_id' => $record['parent'] ?? null]);
    }
})->name('cp.categories.arrange');
Route::prefix('tabby')->group(function () {
    Route::match(['get', 'post'], 'success', [TabbyController::class, 'success'])->name('webhooks.tabby.success');
    Route::match(['get', 'post'], 'cancel', [TabbyController::class, 'cancel'])->name('webhooks.tabby.cancel');
    Route::match(['get', 'post'], 'failure', [TabbyController::class, 'failure'])->name('webhooks.tabby.failure');
    Route::match(['get', 'post'], 'capture', [TabbyController::class, 'capture'])->name('webhooks.tabby.capture');
});

