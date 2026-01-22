<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Transaction;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use Tabby\Services\TabbyService;
use Tabby\Models\Buyer;
use Tabby\Models\Order;
use Tabby\Models\OrderItem;
use Tabby\Models\ShippingAddress;
use Tabby\Exceptions\TabbyApiException;
use Tabby\Models\BuyerHistory;
use Tabby\Models\OrderHistory;
use Illuminate\Support\Facades\Log;

class PayTransactionViaTabby
{
    public static function run(Transaction $transaction)
    {
        try {
            $isTestMode = config('tabby.is_test_mode', true);
            
            $tabbyService = new TabbyService(
                merchantCode: config('tabby.merchant_code'),
                publicKey: config('tabby.public_key'),
                secretKey: config('tabby.secret_key'),
                currency: 'SAR'
            );
         
            // Get user's order history (5-10 previous orders) - exclude current transaction
            $previousOrders = $transaction->user->reservations()
                ->where('id', '!=', $transaction->transactionable_id) // Exclude current reservation
                ->orderBy('created_at', 'desc')
                ->limit(10) // Get up to 10 previous orders
                ->get();

            // Get user's registration date or first order date
            $registeredSince = $transaction->user->created_at ?? 
                             $transaction->user->transactions()->oldest()->first()?->created_at ?? 
                             now();

            // Count successful orders for loyalty level - exclude current transaction
            $successfulOrdersCount = $transaction->user->reservations()
                ->where('status', ReservationStatus::COMPLETED->value)
                ->where('id', '!=', $transaction->transactionable_id) // Exclude current reservation
                ->count();
                Log::info('successfulOrdersCount', ['successfulOrdersCount'=>$successfulOrdersCount]);
            // Log loyalty level calculation for debugging
            Log::info('Tabby loyalty level calculation', [
                'user_id' => $transaction->user->id,
                'current_reservation_id' => $transaction->transactionable_id,
                'successful_orders_count' => $successfulOrdersCount,
                'total_reservations' => $transaction->user->reservations()->count(),
                'completed_reservations' => $transaction->user->reservations()->where('status', ReservationStatus::COMPLETED->value)->count()
            ]);

            // Create buyer history
            $buyerHistory = new BuyerHistory(
                loyaltyLevel: $successfulOrdersCount,
                registeredSince: $registeredSince->toIso8601String()
            );

            $buyer = new Buyer(
                // phone: '+966500000002',
                // // email: 'otp.rejected@tabby.ai',
                // email :'otp.success@tabby.ai',
                // name: 'John Doe',
                
                phone: $transaction->user->phone,
                email: $transaction->user->email,
                name: $transaction->user->name,
            );

            $amount = (float) $transaction->price->getAmount() / 100;
            
            // Enhanced order item with more descriptive information
            $orderItem = new OrderItem(
                title: 'Reservation Payment',
                description: 'Payment for reservation #' . $transaction->id,
                quantity: 1,
                unitPrice: number_format($amount, 2, '.', ''),
                referenceId: (string) $transaction->id,
                category: 'reservation'
            );

            $order = new Order(
                referenceId: (string) $transaction->id,
                items: [$orderItem]
            );

            $city = $transaction->user->city;
            if (is_string($city) && str_starts_with($city, '{')) {
                $cityData = json_decode($city, true);
                $city = $cityData['name']['ar'] ?? 'Riyadh';
            } elseif (is_object($city)) {
                $city = $city->name['ar'] ?? 'Riyadh';
            }

            $shippingAddress = new ShippingAddress(
                city: $city ?? 'Riyadh',
                address: $transaction->user->address ?? 'N/A',
                zip: $transaction->user->zip ?? '12345',
            );

            // Create order history array from previous orders (5-10 orders)
            $orderHistoryArray = [];
            $orderHistory = null;
            
            if ($previousOrders->isNotEmpty()) {
                // Create order history for up to 10 previous orders
                foreach ($previousOrders->take(10) as $previousOrder) {
                    $previousTransaction = $previousOrder->transaction; // Use transaction() method
                    
                    if ($previousTransaction) {
                        $previousAmount = (float) $previousTransaction->price->getAmount() / 100;
                        $previousOrderItem = new OrderItem(
                            title: 'Reservation Payment',
                            description: 'Payment for reservation #' . $previousTransaction->id,
                            quantity: 1,
                            unitPrice: number_format($previousAmount, 2, '.', ''),
                            referenceId: (string) $previousTransaction->id,
                            category: 'reservation'
                        );
                        $orderHistoryArrayAmount = (float) $previousTransaction->price->getAmount() / 100;
                        $orderHistoryArray[] = new OrderHistory(
                            amount: number_format($orderHistoryArrayAmount, 2, '.', ''),
                            buyer: $buyer,
                            shippingAddress: $shippingAddress,
                            purchasedAt: $previousOrder->created_at->toIso8601String(),
                            status: self::mapStatusToTabby($previousOrder->status->value),
                            items: [$previousOrderItem]

                        );
                    }
                }
                
                // Pass all order histories to Tabby (5-10 previous orders)
                if (!empty($orderHistoryArray)) {
                    // Pass all order histories to Tabby
                    $orderHistory = $orderHistoryArray;
                }
            }

            // Log order history for debugging
            Log::info('Tabby order history', [
                'user_id' => $transaction->user->id,
                'current_reservation_id' => $transaction->transactionable_id,
                'previous_orders_count' => $previousOrders->count(),
                'order_history_array_count' => count($orderHistoryArray),
                'order_history_type' => is_array($orderHistory) ? 'array' : (is_object($orderHistory) ? 'object' : 'null'),
                'all_previous_orders' => collect($orderHistoryArray)->map(function($history, $index) {
                    return [
                        'index' => $index,
                        'amount' => $history->amount ?? 'unknown',
                        'status' => $history->status ?? 'unknown',
                        'purchased_at' => $history->purchasedAt ?? 'unknown'
                    ];
                })->toArray()
            ]);

            $successUrl = route('webhooks.tabby.success');
            $cancelUrl = route('webhooks.tabby.cancel');
            $failureUrl = route('webhooks.tabby.failure');
            
            // Log the request payload for debugging
            Log::info('Tabby createSession request', [
                'amount' => number_format($amount, 2, '.', ''),
                'buyer' => $buyer->toArray(),
                'order' => $order->toArray(),
                'shippingAddress' => $shippingAddress->toArray(),
                'buyerHistory' => $buyerHistory->toArray(),
                'orderHistory' => is_array($orderHistory) ? 
                    collect($orderHistory)->map(fn($h) => $h->toArray())->toArray() : 
                    ($orderHistory ? $orderHistory->toArray() : null),
                'callbacks' => [
                    'success' => $successUrl,
                    'cancel' => $cancelUrl,
                    'failure' => $failureUrl
                ]
            ]);

            $checkoutSession = $tabbyService->createSession(
                amount: number_format($amount, 2, '.', ''),
                buyer: $buyer,
                order: $order,
                description: "string",
                shippingAddress: $shippingAddress,
                successCallback: $successUrl,
                cancelCallback: $cancelUrl,
                failureCallback: $failureUrl,
                buyerHistory: $buyerHistory,
                orderHistory: $orderHistory,
                lang: app()->getLocale()
            );
            
            // Debug the response
            $sessionData = $checkoutSession->toArray();
            Log::info('Tabby createSession response', $sessionData);
            
            // =====================================================================
            // Check for errors following the recommended pattern from CheckoutTrait
            // =====================================================================
            
            // Check for explicit rejection status
            if (isset($sessionData['status']) && $sessionData['status'] === 'rejected') {
                Log::warning('Tabby session explicitly rejected', [
                    'transaction_id' => $transaction->id,
                    'session_data' => $sessionData
                ]);
                
                // Get warnings array if available
                $warnings = $sessionData['warnings'] ?? [];
                
                // Check if we have an explicit rejection reason
                $rejectionReason = null;
                if (isset($sessionData['configuration']['products']['installments'])) {
                    $installmentsData = $sessionData['configuration']['products']['installments'];
                    $isAvailable = $installmentsData['is_available'] ?? false;
                    
                    if (!$isAvailable) {
                        $rejectionReason = $installmentsData['rejection_reason'] ?? 'not_available';
                    }
                }
                
              
                $errorMessage = self::mapRejectionReason($rejectionReason);
              
                
                // Update transaction status
                self::markTransactionFailed($transaction, 'rejected', $errorMessage, [
                    'rejection_reason' => $rejectionReason,
                    'warnings' => $warnings,
                    'original_response' => $sessionData
                ]);
                
                return response()->json([
                    'status' => 400,
                    'message' => $errorMessage,
                    'payment_status' => ReservationPaymentStatus::CANCELED->value,
                    'reservation_status' => ReservationStatus::CANCELED->value,
                    'errors' => [
                        'disable_tabby' => true,
                        'warnings' => $warnings,
                        'rejection_reason' => $rejectionReason,
                        'rejection_message' => $errorMessage,
                        'detailed_error' => $errorMessage
                    ]
                ], 400);
            }
            
            // Check if products are available
            if (empty($sessionData['configuration']['available_products'])) {
                Log::warning('Tabby no available products', [
                    'transaction_id' => $transaction->id,
                    'session_data' => $sessionData
                ]);
                
                // Check if we have a specific rejection reason for installments
                $rejectionReason = null;
                if (isset($sessionData['configuration']['products']['installments'])) {
                    $installmentsData = $sessionData['configuration']['products']['installments'];
                    $isAvailable = $installmentsData['is_available'] ?? false;
                    
                    if (!$isAvailable) {
                        $rejectionReason = $installmentsData['rejection_reason'] ?? 'not_available';
                    }
                }
                
                // Map the rejection reason to a user-friendly message
                $mappedReason = self::mapRejectionReason($rejectionReason ?? 'not_available');
                
                // Update transaction status
                self::markTransactionFailed($transaction, 'no_options', $mappedReason, $sessionData);
                
                return response()->json([
                    'status' => 400,
                    'message' => $mappedReason . 'ddddd',
                    'payment_status' => ReservationPaymentStatus::CANCELED->value,
                    'reservation_status' => ReservationStatus::CANCELED->value,
                    'errors' => [
                        'disable_tabby' => true,
                        'rejection_reason' => $rejectionReason,
                        'rejection_message' => $mappedReason
                    ]
                ], 400);
            }
            
            // Get the installment URL from a valid product
            $availableProducts = $sessionData['configuration']['available_products'];
            $webUrl = null;
            
            // Try to get web URL from installments first
            if (isset($availableProducts['installments']) && !empty($availableProducts['installments'])) {
                $webUrl = $availableProducts['installments'][0]['web_url'] ?? null;
            }
            
            // If no installments, try to get web URL from any available product
            if (!$webUrl) {
                foreach ($availableProducts as $productType => $productDetails) {
                    if (isset($productDetails[0]['web_url'])) {
                        $webUrl = $productDetails[0]['web_url'];
                        break;
                    }
                }
            }
            
            // Final check if we have a web URL
            if (!$webUrl) {
                Log::warning('Tabby no web URL found', [
                    'transaction_id' => $transaction->id,
                    'available_products' => $availableProducts
                ]);
                
                // Update transaction status
                self::markTransactionFailed($transaction, 'no_web_url', 'No payment URL found in Tabby response', $sessionData);
                
                return response()->json([
                    'status' => 400,
                    'message' => 'No payment URL found in Tabby response',
                    'payment_status' => ReservationPaymentStatus::CANCELED->value,
                    'reservation_status' => ReservationStatus::CANCELED->value,
                    'errors' => ['disable_tabby' => true]
                ], 400);
            }
            
            // If we got here, we have a successful session with a valid web URL
            Log::info('Tabby payment session created successfully', [
                'transaction_id' => $transaction->id,
                'payment_id' => $sessionData['payment']['id'] ?? null,
                'web_url' => $webUrl
            ]);

            // Update transaction with successful data
            $transaction->update([
                'status' => ReservationPaymentStatus::PENDING->value,
                'meta_data' => [
                    'gateway' => 'tabby',
                    'invoiceId' => $sessionData['payment']['id'] ?? null,
                    'invoiceURL' => $webUrl,
                    'sessionId' => $sessionData['id'] ?? null,
                    'paid_at' => $sessionData['payment']['created_at'] ?? null,
                    'expires_at' => $sessionData['payment']['expires_at'] ?? null,
                    'original_response' => $sessionData
                ]
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Payment session created successfully',
                'redirect_url' => $webUrl,
                'session_id' => $sessionData['id'] ?? null,
                'payment_id' => $sessionData['payment']['id'] ?? null,
                'payment_status' => ReservationPaymentStatus::PENDING->value
            ], 200);
            
        } catch (TabbyApiException $tabbyException) {
            // Log the TabbyApiException with its original message and data
            Log::error('Tabby API Exception', [
                'transaction_id' => $transaction->id,
                'message' => $tabbyException->getMessage(),
                'context' => $tabbyException->context(),
                'code' => $tabbyException->getCode()
            ]);
            
            // Get detailed error message
            $context = $tabbyException->context();
            $errorMessage = self::extractErrorMessage($context['response'] ?? [], $tabbyException->getMessage());
            
            // Update transaction status
            self::markTransactionFailed(
                $transaction, 
                'api_exception', 
                $errorMessage, 
                [
                    'response' => $context['response'] ?? [],
                    'status_code' => $tabbyException->getCode()
                ]
            );
            
            return response()->json([
                'status' => $tabbyException->getCode() ?? 400,
                'message' => $errorMessage,
                'payment_status' => ReservationPaymentStatus::CANCELED->value,
                'reservation_status' => ReservationStatus::CANCELED->value,
                'errors' => array_merge(
                    ['disable_tabby' => true], 
                    ['detailed_error' => $errorMessage],
                    $context['response'] ?? []
                )
            ], $tabbyException->getCode() ?? 400);
            
        } catch (\Exception $e) {
            // General exception handling (not TabbyApiException)
            Log::error('Tabby payment error', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Update transaction status
            self::markTransactionFailed($transaction, 'exception', $e->getMessage());
            
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'payment_status' => ReservationPaymentStatus::CANCELED->value,
                'reservation_status' => ReservationStatus::CANCELED->value,
                'errors' => [
                    'disable_tabby' => true,
                    'detailed_error' => $e->getMessage()
                ]
            ], 400);
        }
    }
    
    /**
     * Mark transaction as failed and update related models
     */
    private static function markTransactionFailed(
        Transaction $transaction, 
        string $reason, 
        string $message, 
        array $additionalData = []
    ): void {
        // Update transaction status
        $transaction->update([
            'status' => ReservationPaymentStatus::CANCELED->value,
            'meta_data' => array_merge([
                'status' => 'failed',
                'failure_reason' => $reason,
                'failure_message' => $message,
                'failed_at' => now()->toIso8601String(),
            ], $additionalData)
        ]);
        
        // Update reservation status if it exists
        if ($transaction->transactionable) {
            $transaction->transactionable->update([
                'status' => ReservationStatus::CANCELED->value
            ]);
        }
    }
    
    /**
     * Extract a meaningful error message from errors or warnings
     */
    private static function extractErrorMessage($errorData, string $defaultMessage): string {
        // If we have no data, return the default message
        if (empty($errorData)) {
            return $defaultMessage;
        }
        
        // If it's a string, return it
        if (is_string($errorData)) {
            return $errorData;
        }
        
        // If it's an array of errors or warnings
        if (is_array($errorData)) {
            // Check for common Tabby warning structure
            if (isset($errorData[0]['message'])) {
                return $errorData[0]['message'];
            }
            
            // For errors array with field->message structure
            foreach ($errorData as $field => $fieldError) {
                if (is_string($fieldError)) {
                    return $field . ': ' . $fieldError;
                }
                if (is_array($fieldError) && isset($fieldError['message'])) {
                    return $field . ': ' . $fieldError['message'];
                }
            }
            
            // Fall back to JSON representation
            return json_encode($errorData);
        }
        
        // Fall back to default message
        return $defaultMessage;
    }
    
    private static function mapStatusToTabby(string $status): string {
        return match ($status) {
            'pending' => 'new',
            'processing' => 'processing',
            'completed' => 'complete',
            'canceled' => 'canceled',
            'not_performed' => 'unknown',
            default => 'unknown'
        };
    }
    
    /**
     * Map Tabby rejection reason to user-friendly message
     * Based on: https://docs.tabby.ai/pay-in-4-custom-integration/checkout-flow
     */
    private static function mapRejectionReason(string $reason): string {
        $rejectionMessages = [
            'not_available' => __("validation.tabby.not_available"),
            'order_amount_too_high'=>__("validation.tabby.order_amount_too_high"),
            'order_amount_too_low'=> __("validation.tabby.order_amount_too_low"),
        ];
        
        return $rejectionMessages[$reason] ?? "Payment was rejected: $reason";
    }
}