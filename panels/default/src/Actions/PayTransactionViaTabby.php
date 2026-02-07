<?php

namespace App\DefaultPanel\Actions;

use Exception;
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
         
            // =====================================================================
            // VALIDATE BUYER INFORMATION BEFORE CREATING SESSION
            // =====================================================================
            $user = $transaction->user;
            $buyerPhone = $user->phone ?? null;
            $buyerEmail = $user->email ?? null;
            $buyerName = $user->name ?? null;

            // Validate required buyer information
            if (empty($buyerPhone) || empty($buyerEmail) || empty($buyerName)) {
                Log::error('Tabby payment: Missing buyer information', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'has_phone' => !empty($buyerPhone),
                    'has_email' => !empty($buyerEmail),
                    'has_name' => !empty($buyerName),
                ]);
                
                self::markTransactionFailed(
                    $transaction, 
                    'missing_buyer_info', 
                    'Buyer information (name, email, or phone) is missing. Please update your profile.',
                    [
                        'missing_fields' => [
                            'phone' => empty($buyerPhone),
                            'email' => empty($buyerEmail),
                            'name' => empty($buyerName),
                        ]
                    ]
                );
                
                return response()->json([
                    'status' => 400,
                    'message' => 'Please complete your profile information (name, email, and phone) before making a payment.',
                    'payment_status' => ReservationPaymentStatus::CANCELED->value,
                    'reservation_status' => ReservationStatus::CANCELED->value,
                    'errors' => [
                        'missing_buyer_info' => true,
                        'missing_fields' => [
                            'phone' => empty($buyerPhone),
                            'email' => empty($buyerEmail),
                            'name' => empty($buyerName),
                        ]
                    ]
                ], 400);
            }

            // Validate phone format (should be E.164 format for Tabby)
            if (!preg_match('/^\+[1-9]\d{1,14}$/', $buyerPhone)) {
                Log::warning('Tabby payment: Invalid phone format, attempting to fix', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'phone' => $buyerPhone,
                ]);
                
                // Try to format phone if it's missing the + prefix
                if (!str_starts_with($buyerPhone, '+')) {
                    // If phone starts with country code without +, add it
                    if (str_starts_with($buyerPhone, '966')) {
                        $buyerPhone = '+' . $buyerPhone;
                    } else {
                        // Default to Saudi Arabia if no country code
                        $buyerPhone = '+966' . ltrim($buyerPhone, '0');
                    }
                }
            }

            // Validate email format
            if (!filter_var($buyerEmail, FILTER_VALIDATE_EMAIL)) {
                Log::error('Tabby payment: Invalid email format', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'email' => $buyerEmail,
                ]);
                
                self::markTransactionFailed(
                    $transaction, 
                    'invalid_email', 
                    'Invalid email address. Please update your profile.',
                    ['email' => $buyerEmail]
                );
                
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid email address. Please update your profile.',
                    'payment_status' => ReservationPaymentStatus::CANCELED->value,
                    'reservation_status' => ReservationStatus::CANCELED->value,
                    'errors' => ['invalid_email' => true]
                ], 400);
            }

            // Get user's order history (5-10 previous orders) - exclude current transaction
            $previousOrders = $transaction->user->reservations()
                ->where('id', '!=', $transaction->transactionable_id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get user's registration date or first order date
            $registeredSince = $transaction->user->created_at ?? 
                             $transaction->user->transactions()->oldest()->first()?->created_at ?? 
                             now();

            // Count successful orders for loyalty level - exclude current transaction
            $successfulOrdersCount = $transaction->user->reservations()
                ->where('status', ReservationStatus::COMPLETED->value)
                ->where('id', '!=', $transaction->transactionable_id)
                ->count();
                
            Log::info('successfulOrdersCount', ['successfulOrdersCount'=>$successfulOrdersCount]);
            
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

            // Create buyer with validated information
            $buyer = new Buyer(
                phone: $buyerPhone,
                email: $buyerEmail,
                name: $buyerName,
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
                foreach ($previousOrders->take(10) as $previousOrder) {
                    $previousTransaction = $previousOrder->transaction;
                    
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
                
                if (!empty($orderHistoryArray)) {
                    $orderHistory = $orderHistoryArray;
                }
            }

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
            // CHECK BUYER INFO IN RESPONSE
            // =====================================================================
            if (isset($sessionData['payment']['buyer'])) {
                $responseBuyer = $sessionData['payment']['buyer'];
                $hasBuyerInfo = !empty($responseBuyer['name']) && 
                                !empty($responseBuyer['email']) && 
                                !empty($responseBuyer['phone']);
                
                if (!$hasBuyerInfo) {
                    Log::warning('Tabby payment: Buyer information missing in response', [
                        'transaction_id' => $transaction->id,
                        'response_buyer' => $responseBuyer,
                        'sent_buyer' => $buyer->toArray(),
                    ]);
                }
            }
            
            // =====================================================================
            // CHECK FOR ERRORS FOLLOWING THE RECOMMENDED PATTERN FROM CHECKOUTTRAIT
            // =====================================================================
            
            // Check for explicit rejection status
            if (isset($sessionData['status']) && $sessionData['status'] === 'rejected') {
                Log::warning('Tabby session explicitly rejected', [
                    'transaction_id' => $transaction->id,
                    'session_data' => $sessionData
                ]);
                
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
                
                // Store payment ID even if rejected - so we can check status later
                $paymentId = $sessionData['payment']['id'] ?? null;
                $responseBuyer = $sessionData['payment']['buyer'] ?? [];
                
                // Update transaction status
                self::markTransactionFailed($transaction, 'rejected', $errorMessage, [
                    'rejection_reason' => $rejectionReason,
                    'warnings' => $warnings,
                    'original_response' => $sessionData,
                    'gateway' => 'tabby',
                    'invoiceId' => $paymentId, // Store payment ID for later checking
                    'sessionId' => $sessionData['id'] ?? null,
                    'paid_at' => $sessionData['payment']['created_at'] ?? null,
                    'expires_at' => $sessionData['payment']['expires_at'] ?? null,
                    'buyer_info_missing_in_response' => empty($responseBuyer['name']) || 
                                                       empty($responseBuyer['email']) || 
                                                       empty($responseBuyer['phone']),
                    'sent_buyer_info' => $buyer->toArray(), // Store what we sent
                    'response_buyer_info' => $responseBuyer, // Store what Tabby returned
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
                        'detailed_error' => $errorMessage,
                        'payment_id' => $paymentId, // Include payment ID in response
                    ]
                ], 400);
            }
            
            // Check if products are available
            if (empty($sessionData['configuration']['available_products'])) {
                Log::warning('Tabby no available products', [
                    'transaction_id' => $transaction->id,
                    'session_data' => $sessionData
                ]);
                
                $rejectionReason = null;
                if (isset($sessionData['configuration']['products']['installments'])) {
                    $installmentsData = $sessionData['configuration']['products']['installments'];
                    $isAvailable = $installmentsData['is_available'] ?? false;
                    
                    if (!$isAvailable) {
                        $rejectionReason = $installmentsData['rejection_reason'] ?? 'not_available';
                    }
                }
                
                $mappedReason = self::mapRejectionReason($rejectionReason ?? 'not_available');
                
                // Store payment ID for later checking
                $paymentId = $sessionData['payment']['id'] ?? null;
                
                self::markTransactionFailed($transaction, 'no_options', $mappedReason, [
                    'gateway' => 'tabby',
                    'invoiceId' => $paymentId,
                    'sessionId' => $sessionData['id'] ?? null,
                    'original_response' => $sessionData
                ]);
                
                return response()->json([
                    'status' => 400,
                    'message' => $mappedReason,
                    'payment_status' => ReservationPaymentStatus::CANCELED->value,
                    'reservation_status' => ReservationStatus::CANCELED->value,
                    'errors' => [
                        'disable_tabby' => true,
                        'rejection_reason' => $rejectionReason,
                        'rejection_message' => $mappedReason,
                        'payment_id' => $paymentId,
                    ]
                ], 400);
            }
            
            // Get the installment URL from a valid product
            $availableProducts = $sessionData['configuration']['available_products'];
            $webUrl = null;
            
            if (isset($availableProducts['installments']) && !empty($availableProducts['installments'])) {
                $webUrl = $availableProducts['installments'][0]['web_url'] ?? null;
            }
            
            if (!$webUrl) {
                foreach ($availableProducts as $productType => $productDetails) {
                    if (isset($productDetails[0]['web_url'])) {
                        $webUrl = $productDetails[0]['web_url'];
                        break;
                    }
                }
            }
            
            if (!$webUrl) {
                Log::warning('Tabby no web URL found', [
                    'transaction_id' => $transaction->id,
                    'available_products' => $availableProducts
                ]);
                
                $paymentId = $sessionData['payment']['id'] ?? null;
                
                self::markTransactionFailed($transaction, 'no_web_url', 'No payment URL found in Tabby response', [
                    'gateway' => 'tabby',
                    'invoiceId' => $paymentId,
                    'sessionId' => $sessionData['id'] ?? null,
                    'original_response' => $sessionData
                ]);
                
                return response()->json([
                    'status' => 400,
                    'message' => 'No payment URL found in Tabby response',
                    'payment_status' => ReservationPaymentStatus::CANCELED->value,
                    'reservation_status' => ReservationStatus::CANCELED->value,
                    'errors' => [
                        'disable_tabby' => true,
                        'payment_id' => $paymentId,
                    ]
                ], 400);
            }
            
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
            Log::error('Tabby API Exception', [
                'transaction_id' => $transaction->id,
                'message' => $tabbyException->getMessage(),
                'context' => $tabbyException->context(),
                'code' => $tabbyException->getCode()
            ]);
            
            $context = $tabbyException->context();
            $errorMessage = self::extractErrorMessage($context['response'] ?? [], $tabbyException->getMessage());
            
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
            
        } catch (Exception $e) {
            Log::error('Tabby payment error', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
        $transaction->update([
            'status' => ReservationPaymentStatus::CANCELED->value,
            'meta_data' => array_merge([
                'status' => 'failed',
                'failure_reason' => $reason,
                'failure_message' => $message,
                'failed_at' => now()->toIso8601String(),
            ], $additionalData)
        ]);
        
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
        if (empty($errorData)) {
            return $defaultMessage;
        }
        
        if (is_string($errorData)) {
            return $errorData;
        }
        
        if (is_array($errorData)) {
            if (isset($errorData[0]['message'])) {
                return $errorData[0]['message'];
            }
            
            foreach ($errorData as $field => $fieldError) {
                if (is_string($fieldError)) {
                    return $field . ': ' . $fieldError;
                }
                if (is_array($fieldError) && isset($fieldError['message'])) {
                    return $field . ': ' . $fieldError['message'];
                }
            }
            
            return json_encode($errorData);
        }
        
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