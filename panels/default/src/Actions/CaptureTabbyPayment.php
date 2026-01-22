<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Transaction;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\CatalogModule\Models\Reservation;
use Tabby\Services\TabbyService;
use Tabby\Exceptions\TabbyApiException;
use Illuminate\Support\Facades\Log;

class CaptureTabbyPayment
{
    public static function run(Transaction $transaction)
    {
        try {
            // Get payment ID from metadata
            $paymentId = $transaction->meta_data['invoiceId'] ?? null;
            
            if (!$paymentId) {
                Log::error('Tabby capture: Missing payment ID in transaction metadata', [
                    'transaction_id' => $transaction->id,
                    'meta_data' => $transaction->meta_data
                ]);
                return response()->json([
                    'error' => 'Missing payment ID',
                    'message' => 'Payment ID not found in transaction metadata'
                ], 400);
            }

            $tabbyService = new TabbyService(
                merchantCode: config('tabby.merchant_code'),
                publicKey: config('tabby.public_key'),
                secretKey: config('tabby.secret_key'),
                currency: 'SAR'
            );

            // Capture the payment
            $response = $tabbyService->capturePayment(
                paymentId: $paymentId,
                amount: (float) $transaction->price->getAmount() / 100,
                referenceId: (string) $transaction->id
            );

            // Log the capture response
            Log::info('Tabby payment capture response', [
                'transaction_id' => $transaction->id,
                'payment_id' => $paymentId,
                'response' => $response->toArray()
            ]);

            // Update transaction status
            $transaction->update([
                'status' => ReservationPaymentStatus::PAID->value,
                'meta_data' => array_merge($transaction->meta_data, [
                    'captured_at' => now()->toIso8601String(),
                    'capture_response' => $response->toArray()
                ])
            ]);

            // Update reservation status if it exists
            if ($transaction->transactionable && $transaction->transactionable instanceof Reservation) {
                $transaction->transactionable->update([
                    'status' => ReservationStatus::PENDING->value
                ]);
            }

            return response()->json([
                'status' => ReservationPaymentStatus::PAID->value,
                'message' => 'Payment captured successfully'
            ]);

        } catch (TabbyApiException $e) {
            Log::error('Tabby capture API error', [
                'transaction_id' => $transaction->id,
                'payment_id' => $paymentId ?? null,
                'error' => $e->getMessage(),
                'context' => $e->context()
            ]);

            // Update transaction status
            $transaction->update([
                'status' => ReservationPaymentStatus::CANCELED->value,
                'meta_data' => array_merge($transaction->meta_data, [
                    'capture_failed_at' => now()->toIso8601String(),
                    'capture_error' => $e->getMessage(),
                    'context' => $e->context()
                ])
            ]);

            return response()->json([
                'error' => 'Payment capture failed',
                'message' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Tabby capture error', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update transaction status
            $transaction->update([
                'status' => ReservationPaymentStatus::CANCELED->value,
                'meta_data' => array_merge($transaction->meta_data, [
                    'capture_failed_at' => now()->toIso8601String(),
                    'capture_error' => $e->getMessage()
                ])
            ]);

            return response()->json([
                'error' => 'Payment capture failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 