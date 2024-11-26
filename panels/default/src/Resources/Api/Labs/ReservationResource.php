<?php

namespace App\DefaultPanel\Resources\Api\Labs;


use App\DefaultPanel\Resources\Api\LightLabResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource {

    public function toArray($request): array {
        $payment_data = $this->transaction->meta_data;
        return [
            'id' => $this->id,
            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
            'lab' => LightLabResource::make($this->reservable),
            'status' => $this->status->getLabel(),
            'service_type' => $this->service_type->getLabel(),
            'reserve_type' => $this->reserve_type->getLabel(),
            'enums' => [
                'service_type' => $this->service_type,
                'reserve_type' => $this->reserve_type,
                'status' => $this->status,
            ],
            'can'=>[
                'rate' => $this->canRate(),
                'reschedule' => $this->canReschedule(),
                'cancel' => $this->canCancel(),
                'report' => $this->canReport(),
                'confirm'=>$this->canConfirm()
            ],
            'date' => $this->date->format('Y-m-d'),
            'from' => $this->from,
            'to' => $this->to,
            $this->mergeWhen($this->rate()->exists(), [
                'rating' => [
                    'rate' => $this?->rate?->rate,
                    'comment' => $this?->rate?->comment,
                ]
            ]),

            $this->mergeWhen($this->report()->exists(), [
                'report_data' => [
                    'reason' => $this?->report?->reason?->name,
                    'comment' => $this?->report?->comment,
                ]
            ]),
            $this->mergeWhen($this->cancellation()->exists(), [
                'cancellation_data' => [
                    'reason' => $this->cancellation?->reason?->name,
                    'comment' => $this->cancellation?->comment,
                ]
            ]),
            'services' => ReservationServiceResource::collection($this->itemsLine),
            'invoice_url'=>route('reservations.invoice',$this),
            'transaction' => [
                'price' => $this->price->format(),
                'gateway' => $payment_data['gateway'],
                'invoiceId' => $payment_data['invoiceId'],
                'invoiceURL' => $payment_data['invoiceURL'],
            ]

        ];
    }


}
