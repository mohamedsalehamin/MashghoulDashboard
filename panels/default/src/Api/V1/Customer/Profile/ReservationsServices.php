<?php

namespace App\DefaultPanel\Api\V1\Customer\Profile;


use Api;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Requests\Api\Customer\Order\ReservationRateRequest;
use App\DefaultPanel\Resources\Api\Customer\ReservationResource;

use App\CatalogModule\Models\Transaction;
use App\DefaultPanel\Actions\CaptureTabbyPayment;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use Illuminate\Support\Facades\Log;
class ReservationsServices {


    public function index() {
        $status = is_array(request('status')) ? request('status') : [request('status')];
        return Api::isOk("rated successfully", ReservationResource::collection(
            auth()->user()->reservations()
                ->when(request()->has('status'), fn($query) => $query->whereIn('status', $status))
                ->when(request()->filled('id'), fn($query) => $query->where('id', request('id')))
                ->when(request()->has('direction'), fn($query) => $query->orderBy('date', request('direction')))
                ->when(request()->has('direction'), fn($query) => $query->orderBy('from', request('direction')))
                ->get()
        ));

    }

    public function show(Reservation $reservation) {
        return Api::isOk("rated successfully", ReservationResource::make($reservation));
    }

    public function rate(ReservationRateRequest $request, Reservation $reservation) {
        $reservation->rate()->create([
            'type' => 'place',
            ...$request->collect('place')
        ]);
        $reservation->rate()->create([
            'type' => 'service',
            ...$request->collect('service')
        ]);
        return Api::isOk("rated successfully");

    }
    public function captureTabby(Reservation $reservation) {
        try {
            // Find the transaction for this reservation
            $transaction = Transaction::where('transactionable_id', $reservation->id)
                ->where('transactionable_type', Reservation::class)
                ->first();

            if (!$transaction) {
                Log::error('Tabby capture: Transaction not found for reservation', [
                    'reservation_id' => $reservation->id,
                    'user_id' => auth()->id()
                ]);
                return Api::isError('Transaction not found for this reservation', 404);
            }
            // Check if transaction is in pending status (ready for capture)
            if ($transaction->status !== ReservationPaymentStatus::PENDING) {
                Log::warning('Tabby capture: Transaction not in pending status', [
                    'transaction_id' => $transaction->id,
                    'reservation_id' => $reservation->id,
                    'current_status' => $transaction->status,
                    'user_id' => auth()->id()
                ]);
                return Api::isError('Payment is not ready for capture', 400);
            }

            // Log the capture request
            Log::info('Tabby capture requested by customer', [
                'transaction_id' => $transaction->id,
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id()
            ]);

            // Update transaction metadata to track customer capture request
            $transaction->update([
                'meta_data' => array_merge($transaction->meta_data, [
                    'customer_capture_requested_at' => now()->toIso8601String(),
                    'customer_capture_request' => request()->all()
                ])
            ]);

            // Proceed with capture
            return CaptureTabbyPayment::run($transaction);

        } catch (\Exception $e) {
            Log::error('Tabby capture error: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id(),
                'exception' => $e
            ]);
            return Api::isError('Failed to capture payment', 500);
        }
    }

}
