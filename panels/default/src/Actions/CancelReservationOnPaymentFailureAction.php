<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Enum\ReservationStatus;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CancelReservationOnPaymentFailureAction
{
    use AsAction;

    /**
     * Cancel a reservation and refund wallet/points used. Frees the slot for others.
     */
    public function handle(Reservation $reservation): void
    {
        try {
            // Refund wallet transactions
            $walletTransactions = $reservation->transactions()
                ->where('meta_data->gateway', 'wallet')
                ->get();

            if ($walletTransactions->isNotEmpty() && $reservation->customer) {
                $totalWalletAmount = 0;

                foreach ($walletTransactions as $transaction) {
                    $amount = $transaction->price instanceof \Cknow\Money\Money
                        ? $transaction->price->formatByDecimal()
                        : (float) $transaction->price;
                    $totalWalletAmount += $amount;
                }

                if ($totalWalletAmount > 0) {
                    $reservation->customer->deposit(
                        amount: $totalWalletAmount,
                        meta: [
                            'description' => [
                                'ar' => 'استرداد مبلغ الحجز رقم ' . $reservation->id . ' بسبب فشل عملية الدفع',
                                'en' => 'Refund for reservation #' . $reservation->id . ' due to payment failure',
                            ],
                        ]
                    );

                    Log::info('Wallet refund processed for payment failure', [
                        'reservation_id' => $reservation->id,
                        'user_id' => $reservation->user_id,
                        'amount' => $totalWalletAmount,
                    ]);
                }
            }

            // Cancel the reservation to free the slot (boot will add timeline)
            $reservation->update([
                'status' => ReservationStatus::CANCELED->value,
            ]);
        } catch (\Exception $e) {
            Log::error('Error canceling reservation on payment failure: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
