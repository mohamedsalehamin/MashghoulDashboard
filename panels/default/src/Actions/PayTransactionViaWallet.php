<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Transaction;
use App\ContentModule\Models\Level;
use App\Models\PointsExchange;
use App\Models\PointsUsage;
use Lorisleiva\Actions\Concerns\AsAction;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;
use Tasawk\Api\Facade\Api;

class PayTransactionViaWallet {
    use AsAction;

    public function handle(Transaction $transaction) {
        $transaction->update(['meta_data' => ['gateway' => 'wallet', 'paid_at' => now()->toString()], 'status' => 'paid']);

        $transactionPrice = $transaction->price->formatByDecimal();

        $transaction->transactionable->customer?->withdraw($transactionPrice,[
            'description'=>[
                'ar'=>__('panel.messages.paid_reservation_no', ['no'=> $transaction->transactionable->id,'amount'=>$transactionPrice],'ar'),
                'en'=>__('panel.messages.paid_reservation_no', ['no'=> $transaction->transactionable->id,'amount'=>$transactionPrice],'en'),
            ]
        ]);
    }

}
