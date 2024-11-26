<?php

namespace App\DefaultPanel\Resources\Api\Orders;

use App\DefaultPanel\Enum\DeliveryMethods;
use App\DefaultPanel\Enum\OrderStatus;
use App\DefaultPanel\Resources\Api\AddressBookResource;
use App\DefaultPanel\Resources\Api\Branches\BranchResource;
use App\DefaultPanel\Resources\Api\Cart\CartProductResource;
use App\DefaultPanel\Resources\Api\RateResource;
use App\DefaultPanel\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class OrdersResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $cart = $this->as_cart;
        $settings = new GeneralSettings();
        return [
            "id" => $this->id,
            "order_number" => $this->order_number,
            "created_date" => $this->created_at->format("Y-m-d h:i a"),
            "due_date" => $this->date?->format("Y-m-d") ?? __("Not yet determined"),
            "status" => __("panel.enums." . $this->status->value),
            "receipt_method" => __("panel.enums." . $this->receipt_method->value),
            "status_code" => $this->status,
            'duration'=> $this->getDuration(),
            'duration_unit'=>'minutes',
            "period" => $this->period == 0 ? __("panel.enums.morning") : __("panel.enums.evening"),
            $this->mergeWhen(in_array($this->receipt_method,[DeliveryMethods::DELIVERY,DeliveryMethods::SUPER_DELIVERY]) , [
                'address' => AddressBookResource::make($this?->address),
            ]),
            'branch' => BranchResource::make($this->branch),

            'payment' => [

                'url' => $this->payment_data['invoiceURL'] ?? null,
                'status' => __("panel.enums." . $this->payment_status->value),
                'status_code' => Str::headline($this->payment_status->value),
                'gateway' => $this->payment_data['gateway'] ?? null,
                'method' => isset($this->payment_data['method']) ? __("panel.enums.{$this->payment_data['method']}") : null,
                'paid_at' => isset($this->payment_data['paid_at']) ? Carbon::parse($this->payment_data['paid_at'])->timezone('africa/cairo')->format('Y-m-d h:i a') : null,
                $this->mergeWhen($this->status == OrderStatus::DELIVERED, [
                    'invoice_url' => route('orders.invoice', $this->id),
                ]),
            ],
            'products' => CartProductResource::collection($this->as_cart->getContent()),


            'customer_name' => $this->customer?->name,


            $this->mergeWhen($this->cancellation()->exists(), [
                'cancellation_reason' => $this->cancellation?->reason?->name
            ]),

            $this->mergeWhen($this?->rated(), [
                'rate' => RateResource::make($this?->rate)
            ]),
            "can_rate" => $this->canRate(),
            'notes' => $this->notes,
            "totals" => $cart->formattedTotals(),

        ];
    }
}
