<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Transaction;
use Lorisleiva\Actions\Concerns\AsAction;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

class PayTransaction {
    use AsAction;

    public function handle(Transaction $transaction) {
        $myfatoorahApiV2 = app(PaymentMyfatoorahApiV2::class);
        $payment_data = $myfatoorahApiV2->getInvoiceURL([
            'CustomerName' => $transaction->user->name,
            'InvoiceValue' => $transaction->price->formatByDecimal(),
            'DisplayCurrencyIso' => 'SAR',
            'CustomerEmail' => $transaction->user->email,
            'CallBackUrl' => route('webhooks.myfatoorah.transactions.callback',['status'=>'success']),
            'ErrorUrl' => route('webhooks.myfatoorah.transactions.callback',['status'=>'error']),
            'MobileCountryCode' => '+965',
            'CustomerMobile' => '512345678',
            'Language' => app()->getLocale(),
            'CustomerReference' => $transaction->id,
            'SourceInfo' => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package ' . MYFATOORAH_LARAVEL_PACKAGE_VERSION
        ]);
        $transaction->update(['meta_data' => [...$payment_data, 'gateway' => 'myfatoorah']]);
        return $transaction;
    }

}
