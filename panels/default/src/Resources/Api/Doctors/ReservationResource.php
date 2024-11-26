<?php

namespace App\DefaultPanel\Resources\Api\Doctors;

use App\ContentModule\Models\Post;
use App\DefaultPanel\Resources\Api\DoctorServiceResource;
use App\DefaultPanel\Resources\Api\LightArticleResource;
use App\DefaultPanel\Resources\Api\LightDoctorResource;
use App\UsersModule\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource {

    public function toArray($request): array {
        $payment_data = $this->transaction?->meta_data;

        return [
            'id' => $this->id,
            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
            'doctor' => LightDoctorResource::make($this->reservable),
            'status' => $this->status->getLabel(),
            'service_type' => $this->service_type->getLabel(),
            'reserve_type' => $this->reserve_type->getLabel(),
            'enums' => [
                'service_type' => $this->service_type,
                'reserve_type' => $this->reserve_type,
                'status' => $this->status,
            ],
            'service' => ReservationServiceResource::make($this->itemsLine()->first()),
            'date' => $this->date->format('Y-m-d'),
            "last_date_to_reserve_revisit"=>$this->date->addDays(7)->format('Y-m-d'),
            'period' => $this->period,
            'shared_medical_tests' => SharedMedicalTestsResource::collection($this->sharedAnalysis),
            'medical_tests' => SharedMedicalTestsResource::collection(auth()->user()->medicalTests()->with('itemsLine')->get()->pluck('itemsLine')->unique("model->id")->flatten()),

            $this->mergeWhen($this->schedule()->exists(), [
                'schedule_date' => [
                    'date' => $this->schedule?->date,
                    'time' => $this->schedule?->period,
                    'causer' => $this->schedule?->causerLabel() ?? '',
                    'status' => $this->schedule?->status,
                ]
            ]),

            $this->mergeWhen($this->revisit()->exists(), [
                'revisit_date' => [
                    'date' => $this->revisit?->date,
                    'time' => $this->revisit?->period,
                ]
            ]),
            $this->mergeWhen($this->rate()->exists(), [
                'rating' => [
                    'rate' => $this->rate?->rate,
                    'comment' => $this->rate?->comment,
                    'date' => $this->rate?->created_at->format("Y-m-d")
                ]
            ]),
            $this->mergeWhen($this->cancellation()->exists(), [
                'cancellation_data' => [
                    'reason' => $this->cancellation?->reason?->name,
                    'comment' => $this->cancellation?->comment,
                    'date' => $this->cancellation?->created_at->format("Y-m-d h:i a")
                ]
            ]),
            $this->mergeWhen($this->report()->exists(), [
                'report_data' => [
                    'reason' => $this->report?->reason?->name,
                    'comment' => $this->report?->comment,
                    'date' => $this->report?->created_at->format("Y-m-d h:i a")
                ]
            ]),

            'can' => [
                'start'=>$this->isRunning(),
                'rate' => $this->canRate(),
                'revisit'=>$this->canRevisit(),
                'reschedule' => $this->canReschedule(),
                'cancel' => $this->canCancel(),
                'report' => $this->canReport(),
                'chat' => $this->date->diffInDays() < 7,
                'prescription'=>$this->prescription()->exists()
            ],
            'invoice_url'=>route('reservations.invoice',$this),
            'transaction' => [
                'price' => $this->price->format(),
                'gateway' => $payment_data['gateway'] ?? '',
                'invoiceId' => $payment_data['invoiceId'] ?? '',
                'invoiceURL' => $payment_data['invoiceURL'] ?? '',
                'paid_at' => isset($payment_data['paid_at']) ? Carbon::parse($payment_data['paid_at'])->timezone('africa/cairo')->format("Y-m-d h:i a") : null
            ]

        ];
    }


}
