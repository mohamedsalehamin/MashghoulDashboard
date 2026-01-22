<?php

use App\CatalogModule\Models\Transaction;
use App\ContentModule\Models\Category;
use App\DefaultPanel\Actions\AddPointToCustomerAction;
use App\DefaultPanel\Actions\AddReservationCommissionAction;
use App\DefaultPanel\Actions\OrderPaidAction;
use App\DefaultPanel\Actions\PayTransactionViaWallet;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Mail\SendEmailNotification;
use App\Notifications\ReservationCreatedSuccessfullyNotification;
use Illuminate\Support\Facades\Route;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;
use App\Http\Controllers\Webhook\TabbyController;

Route::get('testo', function () {
    // $user = \App\Models\User::find(request()->get('id'));
    // $token = request()->get('token');
    // if (request()->filled('token')) {

    //     dd(Firebase::make()
    //         ->setTitle('test')
    //         ->setBody('test')
    //         ->setTokens([$token])
    //         ->do());
    // }
    // dd(Firebase::make()
    //     ->setTitle('test')
    //     ->setBody('test')
    //     ->setTokens([$user->deviceTokens->pluck('token')->toArray()])
    //     ->do());
    Mail::to('ahmed.mostafa.dev.eg@gmail.com')->send(new SendEmailNotification('test', 'test'));
    dd('as');
//    return view('mails.order-invoice',['order' => \App\Models\Order::first()]);
//    $id=request()->get('id')??20;
//    $user = \App\Models\User::find($id);
//dd($user);
//    Notification::send($user, new ReservationTimeIsClosestNotification(App\Models\Order::first()));
    return 200;

    $tabby = new TabbyService();


    dd($tabby->updateWebhook('2dac4151-0880-4a43-9fa1-b0fc70886bd6'));
});

Route::any('webhooks/myfatoorah/transactions/callback', function (MyFatoorahPaymentStatus $myfatoorahApiV2) {
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
Route::prefix('tabby')->group(function () {
    Route::match(['get', 'post'], 'success', [TabbyController::class, 'success'])->name('webhooks.tabby.success');
    Route::match(['get', 'post'], 'cancel', [TabbyController::class, 'cancel'])->name('webhooks.tabby.cancel');
    Route::match(['get', 'post'], 'failure', [TabbyController::class, 'failure'])->name('webhooks.tabby.failure');
    Route::match(['get', 'post'], 'capture', [TabbyController::class, 'capture'])->name('webhooks.tabby.capture');
});

