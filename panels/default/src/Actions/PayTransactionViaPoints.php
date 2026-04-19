<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Transaction;
use App\Models\PointsUsage;
use Lorisleiva\Actions\Concerns\AsAction;

class PayTransactionViaPoints
{
    use AsAction;

    public function handle(Transaction $transaction): void
    {
        $transaction->update(['meta_data' => ['gateway' => 'wallet', 'paid_at' => now()->toString()], 'status' => 'paid']);

        $remaining = (float) $transaction->price->formatByDecimal();

        $exchanges = $transaction->user->pointsExchanges()
            ->where('expired_at', '>', now())
            ->where('reset_price', '>', 0)
            ->where('used', 0)
            ->get();

        foreach ($exchanges as $exchange) {
            if ($remaining <= 0) {
                break;
            }
            $credit = (float) $exchange->reset_price;
            if ($credit <= 0) {
                continue;
            }
            if ($remaining >= $credit) {
                $remaining -= $credit;
                $exchange->update(['used' => true, 'reset_price' => 0]);
            } else {
                $exchange->update(['reset_price' => $credit - $remaining]);
                $remaining = 0;
                break;
            }
        }

        // Remaining amount: deduct from loyalty `points` rows (same balance as User::getTotalPoints()).
        // Treat discount units as 1 loyalty point == 1 currency unit (matches cart validation vs getTotalPoints()).
        if ($remaining > 0) {
            $loyaltyRows = $transaction->user->points()->where('transferred', false)->orderBy('id')->get();
            foreach ($loyaltyRows as $pointRow) {
                if ($remaining <= 0) {
                    break;
                }
                $avail = (float) $pointRow->reset_points;
                if ($avail <= 0) {
                    continue;
                }
                if ($remaining >= $avail) {
                    $remaining -= $avail;
                    $pointRow->update(['transferred' => true, 'reset_points' => 0]);
                } else {
                    $pointRow->update(['reset_points' => $avail - $remaining]);
                    $remaining = 0;
                    break;
                }
            }
        }

        PointsUsage::create([
            'user_id' => $transaction->user_id,
            'reservation_id' => $transaction->transactionable_id,
            'price' => $transaction->price,
        ]);
    }
}
