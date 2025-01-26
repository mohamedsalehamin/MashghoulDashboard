<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Transaction;
use App\ContentModule\Models\Level;
use App\Models\PointsExchange;
use App\Models\PointsUsage;
use Lorisleiva\Actions\Concerns\AsAction;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;
use Tasawk\Api\Facade\Api;

class PayTransactionViaPoints {
    use AsAction;

    public function handle(Transaction $transaction) {
        $transaction->update(['meta_data' => ['gateway' => 'wallet', 'paid_at' => now()->toString()], 'status' => 'paid']);

        $transactionPrice = $transaction->price->formatByDecimal();

        $points = $transaction->user->pointsExchanges()
            ->where('expired_at', '>', now())->where('reset_price', '>', 0)
            ->where('used', 0)
            ->get();


        foreach ($points as $point) {
            if ($transactionPrice >= $point->reset_price) {
                $transactionPrice -= $point->reset_price;
                $point->update(['used' => true, 'reset_price' => 0]);
            } else {
                $point->update(['reset_price' => $point->reset_price - $transactionPrice]);
                break;
            }

        }

        PointsUsage::create([
            'user_id' => $transaction->user_id,
            'reservation_id' => $transaction->transactionable_id,
            'price' => $transaction->price,
        ]);
    }

}
