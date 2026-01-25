<?php

namespace App\Http\Controllers\Webhook;

use Exception;
use App\CatalogModule\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\CaptureTabbyPayment;
use Tabby\Services\TabbyService;
use Tabby\Exceptions\TabbyApiException;

class TabbyController extends Controller
{
    protected $tabbyService;

    public function __construct()
    {
        $this->tabbyService = new TabbyService(
            merchantCode: config('tabby.merchant_code'),
            publicKey: config('tabby.public_key'),
            secretKey: config('tabby.secret_key'),
            currency: 'SAR'
        );
    }

    public function success(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');
            $webhookStatus = $request->input('status');
            
            if (!$paymentId) {
                Log::error('Tabby webhook: Missing payment_id', [
                    'request_data' => $request->all()
                ]);
                return response()->json(['error' => 'Missing payment_id'], 400);
            }

            $transaction = Transaction::where('meta_data->invoiceId', $paymentId)->first();

            if (!$transaction) {
                Log::error('Tabby webhook: Transaction not found', [
                    'payment_id' => $paymentId,
                    'request_data' => $request->all()
                ]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Log the webhook data
            Log::info('Tabby webhook received', [
                'transaction_id' => $transaction->id,
                'payment_id' => $paymentId,
                'webhook_status' => $webhookStatus,
                'request_data' => $request->all()
            ]);

            // Verify payment status with Tabby API (getPayment request)
            try {
                $payment = $this->tabbyService->retrievePayment($paymentId);
                $paymentData = $payment->toArray();

                // Log the payment verification response
                Log::info('Tabby payment verification response', [
                    'transaction_id' => $transaction->id,
                    'payment_id' => $paymentId,
                    'webhook_status' => $webhookStatus,
                    'api_status' => $paymentData['status'] ?? null,
                    'response' => $paymentData
                ]);

                // Check if payment is authorized (API returns "AUTHORIZED" in uppercase)
                if (($paymentData['status'] ?? '') !== 'AUTHORIZED') {
                    Log::warning('Tabby payment not authorized', [
                        'transaction_id' => $transaction->id,
                        'payment_id' => $paymentId,
                        'webhook_status' => $webhookStatus,
                        'api_status' => $paymentData['status'] ?? null,
                        'response' => $paymentData
                    ]);

                    // Update transaction status
                    $transaction->update([
                        'status' => ReservationPaymentStatus::CANCELED->value,
                        'meta_data' => array_merge($transaction->meta_data, [
                            'verification_failed_at' => now()->toIso8601String(),
                            'webhook_status' => $webhookStatus,
                            'api_status' => $paymentData['status'] ?? null,
                            'verification_response' => $paymentData,
                            'failure_reason' => 'Payment not authorized'
                        ])
                    ]);

                    return response()->json([
                        'error' => 'Payment not authorized',
                        'status' => $paymentData['status'] ?? 'unknown'
                    ], 400);
                }

                // Payment is authorized, update transaction status and trigger capture
                $transaction->update([
                    'status' => ReservationPaymentStatus::PENDING->value,
                    'meta_data' => array_merge($transaction->meta_data, [
                        'authorized_at' => now()->toIso8601String(),
                        'webhook_status' => $webhookStatus,
                        'api_status' => $paymentData['status'],
                        'authorization_response' => $request->all(),
                        'verification_response' => $paymentData
                    ])
                ]);

                // Update reservation status
                if ($transaction->transactionable && $transaction->transactionable instanceof Reservation) {
                    $transaction->transactionable->update([
                        'status' => ReservationStatus::PENDING->value
                    ]);
                }

                // Trigger capture immediately as per Tabby checklist
                return CaptureTabbyPayment::run($transaction);

            } catch (TabbyApiException $e) {
                Log::error('Tabby payment verification failed', [
                    'transaction_id' => $transaction->id,
                    'payment_id' => $paymentId,
                    'webhook_status' => $webhookStatus,
                    'error' => $e->getMessage(),
                    'context' => $e->context()
                ]);

                // Update transaction status
                $transaction->update([
                    'status' => ReservationPaymentStatus::CANCELED->value,
                    'meta_data' => array_merge($transaction->meta_data, [
                        'verification_failed_at' => now()->toIso8601String(),
                        'webhook_status' => $webhookStatus,
                        'verification_error' => $e->getMessage(),
                        'context' => $e->context()
                    ])
                ]);

                return response()->json([
                    'error' => 'Payment verification failed',
                    'message' => $e->getMessage()
                ], 500);
            }
        } catch (Exception $e) {
            Log::error('Tabby webhook error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function capture(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');
            
            if (!$paymentId) {
                Log::error('Tabby capture webhook: Missing payment_id', [
                    'request_data' => $request->all()
                ]);
                return response()->json(['error' => 'Missing payment_id'], 400);
            }

            // Find transaction using the original metadata structure
            $transaction = Transaction::where('meta_data->invoiceId', $paymentId)->first();

            if (!$transaction) {
                Log::error('Tabby capture webhook: Transaction not found', [
                    'payment_id' => $paymentId,
                    'request_data' => $request->all()
                ]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Log the capture webhook
            Log::info('Tabby capture webhook received', [
                'transaction_id' => $transaction->id,
                'payment_id' => $paymentId,
                'request_data' => $request->all()
            ]);

            // Proceed with capture (verification already done in success method)
            return CaptureTabbyPayment::run($transaction);

        } catch (Exception $e) {
            Log::error('Tabby capture webhook error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');
            $transaction = Transaction::where('meta_data->invoiceId', $paymentId)->first();

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            $transaction->update([
                'status' => ReservationPaymentStatus::CANCELED->value,
                'meta_data' => array_merge($transaction->meta_data, [
                    'canceled_at' => now()->toIso8601String(),
                    'cancel_response' => $request->all()
                ])
            ]);

            // Update reservation status
            if ($transaction->transactionable && $transaction->transactionable instanceof Reservation) {
                $transaction->transactionable->update([
                    'status' => ReservationStatus::CANCELED->value
                ]);
            }

            return response()->json(['status' => ReservationPaymentStatus::CANCELED->value]);
        } catch (Exception $e) {
            Log::error('Tabby cancel webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function failure(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');
            $transaction = Transaction::where('meta_data->invoiceId', $paymentId)->first();

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            $transaction->update([
                'status' => ReservationPaymentStatus::CANCELED->value,
                'meta_data' => array_merge($transaction->meta_data, [
                    'failed_at' => now()->toIso8601String(),
                    'failure_response' => $request->all()
                ])
            ]);

            // Update reservation status
            if ($transaction->transactionable && $transaction->transactionable instanceof Reservation) {
                $transaction->transactionable->update([
                    'status' => ReservationStatus::CANCELED->value
                ]);
            }

            return response()->json(['status' => ReservationPaymentStatus::CANCELED->value]);
        } catch (Exception $e) {
            Log::error('Tabby failure webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
} 