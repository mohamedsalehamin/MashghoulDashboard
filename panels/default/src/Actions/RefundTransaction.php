<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Transaction;
use Lorisleiva\Actions\Concerns\AsAction;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

class RefundTransaction {
    use AsAction;

    public function handle($invoiceID, $amount) {
        $myfatoorahApiV2 = app(PaymentMyfatoorahApiV2::class);


        return $myfatoorahApiV2->refund(
            keyId: $invoiceID,
            amount: $amount,
            currencyCode: 'SAR',
            comment: 'Refund',
            keyType: 'InvoiceId'
        );


    }



}
