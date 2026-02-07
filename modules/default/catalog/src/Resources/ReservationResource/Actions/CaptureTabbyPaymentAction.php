<?php

namespace App\CatalogModule\Resources\ReservationResource\Actions;

use Filament\Actions\Action;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\CaptureTabbyPayment;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use Filament\Notifications\Notification;
use Tabby\Services\TabbyService;
use Tabby\Exceptions\TabbyApiException;
use Illuminate\Support\Facades\Log;

class CaptureTabbyPaymentAction
{
    public static function make()
    {
        return Action::make('captureTabbyPayment')
            ->label(__('panel.actions.capture_tabby_payment'))
            ->icon('heroicon-o-credit-card')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('panel.actions.capture_tabby_payment'))
            ->modalDescription(__('panel.messages.capture_tabby_payment_description'))
            ->modalSubmitActionLabel(__('panel.actions.capture'))
            ->visible(fn(Reservation $record) => 
                $record->transaction && 
                ($record->transaction->meta_data['gateway'] ?? null) === 'tabby' &&
                in_array($record->transaction->status->value, [
                    ReservationPaymentStatus::PENDING->value,
                    ReservationPaymentStatus::CANCELED->value
                ])
            )
            ->action(function (Reservation $record) {
                $transaction = $record->transaction;
                
                if (!$transaction) {
                    Notification::make()
                        ->title(__('panel.messages.error'))
                        ->body(__('panel.messages.transaction_not_found'))
                        ->danger()
                        ->send();
                    return;
                }

                $paymentId = $transaction->meta_data['invoiceId'] ?? null;
                
                if (!$paymentId) {
                    Notification::make()
                        ->title(__('panel.messages.error'))
                        ->body(__('panel.messages.tabby_payment_id_not_found'))
                        ->danger()
                        ->send();
                    return;
                }

                try {
                    $tabbyService = new TabbyService(
                        merchantCode: config('tabby.merchant_code'),
                        publicKey: config('tabby.public_key'),
                        secretKey: config('tabby.secret_key'),
                        currency: 'SAR'
                    );

                    $payment = $tabbyService->retrievePayment($paymentId);
                    $paymentData = $payment->toArray();
                    $paymentStatus = strtoupper($paymentData['status'] ?? '');

                    $transaction->update([
                        'meta_data' => array_merge($transaction->meta_data, [
                            'manual_check_at' => now()->toIso8601String(),
                            'manual_check_status' => $paymentStatus,
                            'manual_check_response' => $paymentData
                        ])
                    ]);

                    $hasCaptures = !empty($paymentData['captures'] ?? []);
                    $isClosed = $paymentStatus === 'CLOSED';

                    if ($hasCaptures || $isClosed) {
                        $transaction->update([
                            'status' => ReservationPaymentStatus::PAID->value,
                            'meta_data' => array_merge($transaction->meta_data, [
                                'captured_at' => now()->toIso8601String(),
                                'capture_response' => $paymentData,
                                'captured_via' => 'manual_check'
                            ])
                        ]);

                        Notification::make()
                            ->title(__('panel.messages.success'))
                            ->body(__('panel.messages.tabby_payment_already_captured'))
                            ->success()
                            ->send();
                        return;
                    }

                    if ($paymentStatus !== 'AUTHORIZED') {
                        $statusMessage = match($paymentStatus) {
                            'CREATED' => __('panel.messages.tabby_payment_created_not_authorized'),
                            'FAILED', 'CANCELED', 'EXPIRED', 'REJECTED' => __('panel.messages.tabby_payment_failed', ['status' => $paymentStatus]),
                            default => __('panel.messages.tabby_payment_status_unknown', ['status' => $paymentStatus])
                        };

                        Notification::make()
                            ->title(__('panel.messages.payment_not_authorized'))
                            ->body($statusMessage)
                            ->warning()
                            ->send();
                        return;
                    }

                    $result = CaptureTabbyPayment::run($transaction);
                    $resultData = $result->getData(true);

                    if ($result->getStatusCode() === 200 || isset($resultData['status'])) {
                        Notification::make()
                            ->title(__('panel.messages.success'))
                            ->body(__('panel.messages.tabby_payment_captured_successfully'))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title(__('panel.messages.error'))
                            ->body($resultData['message'] ?? __('panel.messages.tabby_payment_capture_failed'))
                            ->danger()
                            ->send();
                    }

                } catch (TabbyApiException $e) {
                    Log::error('Tabby capture action error', [
                        'reservation_id' => $record->id,
                        'transaction_id' => $transaction->id,
                        'payment_id' => $paymentId,
                        'error' => $e->getMessage(),
                        'context' => $e->context()
                    ]);

                    Notification::make()
                        ->title(__('panel.messages.error'))
                        ->body(__('panel.messages.tabby_api_error', ['error' => $e->getMessage()]))
                        ->danger()
                        ->send();
                } catch (\Exception $e) {
                    Log::error('Tabby capture action exception', [
                        'reservation_id' => $record->id,
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    Notification::make()
                        ->title(__('panel.messages.error'))
                        ->body(__('panel.messages.unexpected_error', ['error' => $e->getMessage()]))
                        ->danger()
                        ->send();
                }
            });
    }
}