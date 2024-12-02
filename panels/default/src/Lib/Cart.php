<?php

namespace App\DefaultPanel\Lib;


use App\CatalogModule\Models\Service;
use App\ContentModule\Models\Coupon;
use Cknow\Money\Money;
use Darryldecode\Cart\Cart as CoreCart;
use Darryldecode\Cart\CartCondition;
use Darryldecode\Cart\Helpers\Helpers;
use Darryldecode\Cart\ItemCollection;
use DB;
use App\DefaultPanel\Settings\GeneralSettings;

class Cart extends CoreCart {
    private $orderId;

    public function getSession() {
        return $this->sessionKey;
    }

    public function getQuantityByModelId($id): int {
        return $this->getContent()->where('associatedModel.id', $id)->first()->quantity ?? 0;
    }


    function removeCartCoupon() {
        $this->removeConditionsByType("coupon");
    }

    function applyItem(Service $service, $price, $qty = 1, $attributes = [], $conditions = []) {

        $this->add(
            md5($service->id),
            $service->title,
            $price,
            $qty,
            [
                "original_price" => $price,
                ...$attributes
            ],
            $conditions,
            $service
        );
    }

    function applyCoupon($code): bool {

        !$this->getConditionsByType("coupon")->count() ?: $this->removeConditionsByType("coupon");
        $coupon = Coupon::where('code', trim($code))->first();
        if (!$coupon) {
            return false;
        }
        $total = $coupon->discount_value;
        if ($coupon->discount_type->value == 'percentage') {
            $discount = $this->getServicesTotalIncludeProducts() / 100 * $total;
            $total = min($discount, $coupon->meta_data['max_discount'] ?? $discount);
        }

        $coupon_value = $coupon->formattedValue();
        $conditionData = [
            'name' => $coupon->code,
            'type' => "coupon",
            'target' => "subtotal",
            'value' => "-" . $total,
            'order' => 1,
            'attributes' => [
                'original_value' => "-" . $total,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyReservationsFees($fees): bool {

        !$this->getConditionsByType("reservation_fees")->count() ?: $this->removeConditionsByType("reservation_fees");

        $conditionData = [
            'name' => "reservation_fees",
            'type' => "reservation_fees",
            'target' => "total",
            'value' => $fees,
            'order' => 1,
            'attributes' => [
                'original_value' => $fees,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyProducts($total): bool {

        !$this->getConditionsByType("products")->count() ?: $this->removeConditionsByType("products");
        $conditionData = [
            'name' => 'products',
            'type' => "products",
            'target' => "subtotal",
            'value' => $total,
            'order' => 1,
            'attributes' => [
                'original_value' => "-" . $total,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyDeliveryService($distance): bool {

        $settings = new GeneralSettings();
        $overflowDistance = $distance - $settings->diameter;
        $cost = $settings->delivery_cost;
        if ($overflowDistance > 0) {
            $cost += $overflowDistance * $settings->delivery_cost_for_each_additional_kilometer;
        }
        $conditionData = [
            'name' => 'Delivery service',
            'type' => "delivery",
            'target' => "total",
            'value' => $cost,
            'order' => 2,
            'attributes' => [
                'original_value' => $settings->delivery_cost,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyTakeawayDiscount(): bool {

        $settings = new GeneralSettings();
        if (!$settings->enable_orders_discount_upon_receipt_from_the_branch) {
            return false;
        }
        $conditionData = [
            'name' => 'discount upon receipt from the branch',
            'type' => "takeaway",
            'target' => "subtotal",
            'value' => -$settings->orders_discount_upon_receipt_from_the_branch,
            'order' => 2,
            'attributes' => [
                'original_value' => $settings->orders_discount_upon_receipt_from_the_branch,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }


    function applyTaxes() {
        $settings = new GeneralSettings();
        $value = $settings->taxes;
        $value = "{$value}%";
        !$this->getConditionsByType("taxes")->count() ?: $this->removeConditionsByType("taxes");
        $conditionData = [
            'name' => 'Taxes',
            'type' => "taxes",
            'target' => "total",
            'value' => $value,
            'order' => 1,
            'attributes' => [
                'original_value' => $value,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyDelivery($type, $districtCost = null): bool {
        $settings = new GeneralSettings();
        $value = match ($type) {

            'super_delivery' => ($districtCost > 0 ? $districtCost : $settings->standard_delivery_fees) + $settings->immediate_delivery_fees,
            'delivery' => ($districtCost > 0 ? $districtCost : $settings->standard_delivery_fees),
            default => 0
        };

        !$this->getConditionsByType("delivery")->count() ?: $this->removeConditionsByType("delivery");
        $conditionData = [
            'name' => 'Delivery',
            'type' => "delivery",
            'target' => "total",
            'value' => $value,
            'order' => 2,
            'attributes' => [
                'original_value' => $value,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyCashOnDeliveryCost(): bool {
        $settings = new GeneralSettings();
        $value = $settings->payment_fee_upon_receipt;
        !$this->getConditionsByType("cash_on_delivery_cost")->count() ?: $this->removeConditionsByType("cash_on_delivery_cost");

        $conditionData = [
            'name' => 'cash_on_delivery_cost',
            'type' => "cash_on_delivery_cost",
            'target' => "total",
            'value' => $value,
            'order' => 2,
            'attributes' => [
                'original_value' => $value,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyWalletDiscount($discount): bool {

        !$this->getConditionsByType("wallet")->count() ?: $this->removeConditionsByType("wallet");
        $conditionData = [
            'name' => 'wallet',
            'type' => "wallet",
            'target' => "total",
            'value' => -$discount,
            'order' => 1,
            'attributes' => [
                'original_value' => $discount,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    public
    function setOrderConditions(): static {
        foreach ($this->getConditions() as $condition) {
            DB::table('reservations_conditions')->insert([
                'reservation_id' => $this->getOrderID(),
                'name' => $condition->getName(),
                'type' => $condition->getType(),
                'target' => $condition->getTarget(),
                'value' => $condition->getValue(),
                'order' => $condition->getOrder(),
                'attributes' => json_encode($condition->getAttributes()),
                'model' => null,
            ]);
            if ($condition->getType() == 'coupon') {
                $coupon = Coupon::where('code', $condition->getName())->first();
                $coupon->users()->attach([auth()->id() => [
                    'order_id' => $this->getOrderID(),
                    'used_at' => now(),
                ]]);
            }
        }


        return $this;
    }

    public
    function getOrderItemConditions($item): array {
        $conditions = [];
        foreach ($item->getConditions() as $condition) {
            $conditions[] = [

                'name' => $condition->getName(),
                'type' => $condition->getType(),
                'target' => $condition->getTarget(),
                'value' => $condition->getValue(),
                'order' => $condition->getOrder(),
                'attributes' => json_encode($condition->getAttributes()),
                'model' => null,
            ];
        }
        return $conditions;
    }

    public
    function nativeItems() {
        $ids = [];
        foreach ($this->getContent() as $item) {
            $ids[] = $item['associatedModel']->id;
        }
        return Items::whereIn('id', array_unique($ids))->get();
    }

    private
    function setOrderItemsLine(): static {
        /** @var ItemCollection $item */
        foreach ($this->getContent() as $item) {
            $model = collect($item->associatedModel)->only([
                "id",
                "name",
                "status",
                "price",
                "image",
            ]);
            DB::table('reservations_items_lines')->insert([
                'reservation_id' => $this->getOrderID(),
                'name' => $item->name[app()->getLocale()] ?? $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'attributes' => json_encode($item->attributes),
                'conditions' => json_encode($this->getOrderItemConditions($item)),
                'model' => json_encode($model),
            ]);
        }
        return $this;
    }

    public
    function saveItemsToOrderAndClearAll($orderID) {
        $this->saveItemsToOrder($orderID);
        parent::clearCartConditions();
        parent::clear();
    }

    public
    function saveItemsToOrder($orderID) {
        $this->setOrderID($orderID)
            ->setOrderItemsLine()
            ->setOrderConditions();
    }


    public
    function setOrderID($orderID): static {
        $this->orderId = $orderID;
        return $this;
    }

    public
    function getOrderID() {
        return $this->orderId;
    }

    public
    function foramtedTotal() {
        return $this->getTotal() . ' ' . Ecommerce::currentSymbol();
    }

    protected
    function updateQuantityRelative($item, $key, $value) {
        if (preg_match('/\-/', $value) == 1) {
            $value = (float)str_replace('-', '', $value);

            // we will not allowed to reduced quantity to 0, so if the given value
            // would result to item quantity of 0, we will not do it.
            if (($item[$key] - $value) > 0) {
                $item[$key] -= $value;
            }
        } elseif (preg_match('/\+/', $value) == 1) {
            $item[$key] += (float)str_replace('+', '', $value);
        } else {
            $item[$key] += (float)$value;
        }

        return $item;
    }

    protected
    function updateQuantityNotRelative($item, $key, $value) {
        $item[$key] = (float)$value;
        return $item;
    }


    public
    function itemsTotalWithoutVat() {
        return $this->getContent()->sum(fn($i) => $i->getPriceSum());
    }

    public
    function itemsVatTotal() {
        $itemsVatTotal = $this->getContent()->sum(function (ItemCollection $item) {
            return collect($item->getConditions())->sum(function ($cond) use ($item) {
                return $cond->getCalculatedValue($item->getPriceSum());
            });
        });
        $config = $this->config;
        $config['format_numbers'] = true;
        return (float)Helpers::formatValue($itemsVatTotal, true, $config);
    }

    function format($value) {
        return Money::parse($value)->format();
    }

    public
    function hasDiscount(): bool {
        return $this->getConditionsByType('coupon')->count();
    }

    public
    function hasCashOnDeliveryFees(): bool {
        return $this->getConditionsByType('cash_on_delivery_cost')->count();
    }

    public
    function hasAdminDiscount(): bool {
        return $this->getConditionsByType('discount')->count();
    }

    public
    function hasWalletDiscount(): bool {
        return $this->getConditionsByType('wallet')->count();
    }

    public
    function discount() {
        return $this->getConditionsByType('coupon')?->first()?->getCalculatedValue($this->getContent()->sum(fn(ItemCollection $item) => $item->getPriceSumWithConditions(true)));
    }

    public
    function getProductsTotal() {
        return $this->getConditionsByType('products')?->first()?->getCalculatedValue($this->getContent()->sum(fn(ItemCollection $item) => $item->getPriceSumWithConditions(true)));
    }

    public
    function cashOnDeliveryCost() {

        return $this->getConditionsByType('cash_on_delivery_cost')?->first()?->getValue() * 100;
    }

    public
    function getReservationFees() {

        return $this->getConditionsByType('reservation_fees')?->first()?->getValue() * 100;
    }

    public
    function adminDiscount() {
        return $this->getConditionsByType('discount')?->first()?->getCalculatedValue($this->getSubTotal());
    }

    public
    function walletDiscount() {
        return $this->getConditionsByType('wallet')?->first()?->getValue() * 100;
    }

    public
    function formattedTotals(): array {
        return array_map([$this, 'format'], $this->totals());
    }

    public
    function getServicesTotalIncludeProducts() {
        return $this->totals()['services_total'] + $this->getProductsTotal();
    }

    public
    function totals(): array {
        $items_total_with_options = $this->getContent()->sum(fn(ItemCollection $item) => $item->getPriceSumWithConditions(true));
        return [
            'services_total' => $this->getSubTotalWithoutConditions(),
            "products_total" => $this->getProductsTotal(),
            "discount" => $this->discount(),
            "subtotal" => $this->getSubTotal(),
            "reservation_fees" => $this->getReservationFees(),
            'wallet_discount' => $this->walletDiscount(),
            "total" => $this->getTotal()
        ];
    }
}
