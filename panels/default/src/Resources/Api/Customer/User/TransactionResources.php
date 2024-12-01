<?php

namespace App\DefaultPanel\Resources\Api\Provider\User;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResources extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $transaction_data = $this?->transaction?->meta_data;
        return [
            'id' => $this->transaction->id,
            'reservation' => $this->id,
            'due_date' => $this->created_at->format("Y-m-d H:i a"),
            'invoice_url' => $transaction_data['invoiceURL'] ?? null,
            'invoiceId' => $transaction_data['invoiceId'] ?? null,
            'paid_at' => isset($transaction_data['paid_at']) ? Carbon::parse($transaction_data['paid_at'])->timezone('africa/cairo')->format('Y-m-d h:i a') : null,
            'method' => $transaction_data['method'] ?? null,
            'status' => $this->status->getLabel(),
            'status_code' => $this->status,
            'total' => $this->price->format(),
            'type'=>$this->transaction->status->value =='refunded'?'refunded':'payments'
        ];
    }


}
