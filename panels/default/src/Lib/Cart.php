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
use Str;

class Cart extends CoreCart {
    private $orderId;

    /** @var string|null Last applyCoupon() failure for UI / API messages */
    protected ?string $lastCouponFailureMessage = null;

    public function getSession() {
        return $this->sessionKey;
    }

    public function getQuantityByModelId($id): int {
        return $this->getContent()->where('associatedModel.id', $id)->first()->quantity ?? 0;
    }


    function removeCartCoupon() {
        $this->removeConditionsByType("coupon");
    }

    public function getLastCouponFailureMessage(): ?string {
        return $this->lastCouponFailureMessage;
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

    /**
     * Same numeric base as cost rows services_total + products_total (excludes reservation fee and coupon).
     */
    protected function eligibleBaseServicesPlusProductsMatchingDisplayedTotals(): float {
        $services = (float) $this->getSubTotalWithoutConditions(false);
        $products = (float) ($this->getProductsTotal() ?? 0);

        return $services + $products;
    }

    function applyCoupon($code): bool {

        !$this->getConditionsByType("coupon")->count() ?: $this->removeConditionsByType("coupon");
        $this->lastCouponFailureMessage = null;
        $code = trim((string) $code);
        if ($code === '') {
            return false;
        }
        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            $this->lastCouponFailureMessage = __('validation.api.coupon_code_not_found');
            return false;
        }

        $user = auth()->user();
        if ($user && $coupon->isUserExceedUsageTimes($user)) {
            $this->lastCouponFailureMessage = __('validation.api.coupon_code_exceeds_the_number_of_usages_times');
            return false;
        }
        if (!$coupon->isAvailableToUse()) {
            $this->lastCouponFailureMessage = __('validation.api.coupon_code_is_expired');
            return false;
        }

        // Web checkout sets cart_provider_id in session; API cart uses route model binding without session.
        $routeProvider = request()->route('provider');
        $routeProviderId = is_object($routeProvider) ? ($routeProvider->id ?? null) : $routeProvider;
        $providerId = session('cart_provider_id') ?? $routeProviderId;

        if ($coupon->scope === Coupon::SCOPE_PROVIDERS) {
            if (empty($providerId) || !$coupon->providers()->where('providers.id', $providerId)->exists()) {
                $this->lastCouponFailureMessage = __('validation.api.coupon_not_valid_for_this_provider');
                return false;
            }
        }
        if ($coupon->requested_by === Coupon::REQUESTED_BY_PROVIDER) {
            if (empty($providerId)) {
                $this->lastCouponFailureMessage = __('validation.api.coupon_code_not_found');
                return false;
            }
            if (!empty($coupon->provider_id) && (int) $coupon->provider_id !== (int) $providerId) {
                $this->lastCouponFailureMessage = __('validation.api.coupon_not_valid_for_this_provider');
                return false;
            }
        }

        // Flatten runtime: services and nested products are separate logical entities for coupon base calculations.
        $servicesBase = 0.0;
        $servicesBaseNoSale = 0.0;
        $productsBase = 0.0;
        $productsBaseNoSale = 0.0;

        foreach ($this->getContent() as $item) {
            $service = $item['associatedModel'] ?? null;
            if ($service) {
                $serviceOnSale = isset($service->sale_price, $service->price)
                    && $service->sale_price?->getAmount() > 0
                    && $service->sale_price?->getAmount() < $service->price?->getAmount();
                $qty = (float) ($item['quantity'] ?? 1);
                $serviceEffective = (float) ($item['price'] ?? 0) * max(1.0, $qty);
                $servicesBase += $serviceEffective;
                if (!$serviceOnSale) {
                    $servicesBaseNoSale += $serviceEffective;
                }
            }

            $products = collect($item['attributes']['products'] ?? []);
            foreach ($products as $product) {
                $qty = (int) ($product->quantity ?? $product['quantity'] ?? 1);
                $qty = max(1, $qty);
                $productOnSale = isset($product->sale_price, $product->price)
                    && $product->sale_price?->getAmount() > 0
                    && $product->sale_price?->getAmount() < $product->price?->getAmount();
                $productEffective = $productOnSale
                    ? (float) $product->sale_price->formatByDecimal()
                    : (float) $product->price->formatByDecimal();
                $line = $productEffective * $qty;
                $productsBase += $line;
                if (!$productOnSale) {
                    $productsBaseNoSale += $line;
                }
            }
        }

        $feesBase = (float) $this->getReservationFees();
        // Same numbers as cost breakdown: services_total + products_total (excludes reservation fee).
        $servicesPlusProductsForTotals = $this->eligibleBaseServicesPlusProductsMatchingDisplayedTotals();
        // Cart total for min-order checks: services + products + reservation fee (before coupon).
        $cartTotalBase = $servicesPlusProductsForTotals + $feesBase;

        $eligibleBase = 0.0;
        if ($coupon->requested_by === Coupon::REQUESTED_BY_ADMIN) {
            $eligibleBase = $feesBase;
        } else {
            // Provider coupons: % / fixed discount applies to services & products only (not reservation fee).
            // Fee is still added in cart before the coupon condition; min order can use cartTotalBase incl. fee.
            $applyTarget = $coupon->apply_target ?: Coupon::APPLY_TARGET_ALL_ITEMS;
            $eligibleBase = match ($applyTarget) {
                Coupon::APPLY_TARGET_SERVICES_ONLY => $servicesBase,
                Coupon::APPLY_TARGET_SERVICES_WITHOUT_DISCOUNT => $servicesBaseNoSale,
                Coupon::APPLY_TARGET_PRODUCTS_ONLY => $productsBase,
                Coupon::APPLY_TARGET_PRODUCTS_WITHOUT_DISCOUNT => $productsBaseNoSale,
                Coupon::APPLY_TARGET_ALL_ITEMS_WITHOUT_DISCOUNT => ($servicesBaseNoSale + $productsBaseNoSale),
                // Align with إجمالي قيمة الخدمات + إجمالي المنتجات (not رسوم الحجز).
                default => $servicesPlusProductsForTotals,
            };
        }

        if ($eligibleBase <= 0) {
            $this->lastCouponFailureMessage = $coupon->messageWhenEligibleBaseIsZero();
            return false;
        }

        $min = (float) ($coupon->meta_data['min_order_value'] ?? 0);
        if ($min > 0) {
            $type = (string) ($coupon->meta_data['min_order_value_type'] ?? 'cart_total');
            $baseForMin = $type === 'eligible_base' ? $eligibleBase : $cartTotalBase;
            if ($baseForMin < $min) {
                $this->lastCouponFailureMessage = __('validation.api.coupon_code_min_order_value', ['value' => $min]);
                return false;
            }
        }

        $discountAmount = (float) $coupon->discount_value;
        if ($coupon->discount_type->value === 'percentage') {
            $discount = $eligibleBase / 100 * $discountAmount;
            $cap = $coupon->meta_data['max_discount'] ?? null;
            $discountAmount = min($discount, $cap !== null ? (float) $cap : $discount);
        }
        $discountAmount = min($discountAmount, $eligibleBase);
        if ($discountAmount <= 0) {
            $this->lastCouponFailureMessage = __('validation.api.coupon_discount_not_applicable');
            return false;
        }

        $conditionData = [
            'name' => $coupon->code,
            'type' => "coupon",
            'target' => "subtotal",
            'value' => "-" . $discountAmount,
            // After reservation fees (order 2) so subtotal chain matches cost breakdown order.
            'order' => 4,
            'attributes' => [
                'original_value' => "-" . $discountAmount,
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
            'target' => "subtotal",
            'value' => $fees,
            // Before coupon (order 4) so discount applies after fees in the subtotal chain.
            'order' => 2,
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


    function applyTaxes($percentage) {

        $value = "{$percentage}%";

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

    function applyPointsDiscount($discount): bool {

        !$this->getConditionsByType("points")->count() ?: $this->removeConditionsByType("points");
        $conditionData = [
            'name' => 'points',
            'type' => "points",
            'target' => "total",
            'value' => -$discount,
            'order' => 2,
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
                "sale_price",
                "image",
            ]);
            DB::table('reservations_items_lines')->insert([
                'reservation_id' => $this->getOrderID(),
                'service_id' => $item->associatedModel->id,
                'name' => $item->name[app()->getLocale()] ?? $item->name,
                'price' => $item->price,
                'sale_price' => $item->sale_price,
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
        return Money::parse($value)->formatByDecimal();
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
        $cond = $this->getConditionsByType('coupon')?->first();
        if (!$cond) {
            return 0;
        }
        $v = (string) $cond->getValue();
        // Fixed SAR amount from applyCoupon — do not re-derive via getCalculatedValue(services-only sum).
        if (! str_contains($v, '%')) {
            return abs((float) preg_replace('/[^\d.-]/', '', $v));
        }

        return $cond->getCalculatedValue($this->getContent()->sum(fn (ItemCollection $item) => $item->getPriceSumWithConditions(true)));
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

        return floatval($this->getConditionsByType('reservation_fees')?->first()?->getValue());
    }

    public
    function adminDiscount() {
        return $this->getConditionsByType('discount')?->first()?->getCalculatedValue($this->getSubTotal());
    }

    public function walletDiscount() {

        return (float)$this->getConditionsByType('wallet')?->first()?->getValue();
    }

    public function pointsDiscount() {

        return (float)$this->getConditionsByType('points')?->first()?->getValue();
    }

    /**
     * Key order for formatted totals (API JSON / clients): services & products, then reservation fees,
     * then coupon discount, then subtotal and remaining lines — matches site cost breakdown order.
     *
     * @return list<string>
     */
    protected function totalsOutputKeyOrder(): array {
        return [
            'services_total',
            'products_total',
            'reservation_fees',
            'reservation_fees_include_taxes',
            'reservation_fees_taxes',
            'discount',
            'subtotal',
            'taxes',
            'wallet_discount',
            'points_discount',
            'total_without_reservation_fees_include_taxes',
            'total',
        ];
    }

    public
    function formattedTotals(): array {
        $raw = $this->totals();
        $ordered = [];
        foreach ($this->totalsOutputKeyOrder() as $key) {
            if (array_key_exists($key, $raw)) {
                $ordered[$key] = $this->format($raw[$key]);
            }
        }
        foreach ($raw as $key => $value) {
            if (!array_key_exists($key, $ordered)) {
                $ordered[$key] = $this->format($value);
            }
        }

        return $ordered;
    }

    public
    function getServicesTotalIncludeProducts() {
        return $this->totals()['services_total'] + $this->getProductsTotal();
    }

    public function getNetProfitTotal() {
        return $this->totals()['subtotal'] + $this->totals()['wallet_discount'];
    }

    public function getReservationFeesIncludeTaxes() {
        $reservation_fees = $this->getReservationFees();
        $taxes = $this->getReservationFeesTaxes();
        return $taxes + $reservation_fees;
    }

    public function getReservationFeesTaxes(): float|int {
        $taxCondition = $this->getConditionsByType("taxes")?->first();
        if (!$taxCondition) {
            return 0;
        }
        
        $taxes = (float)Str::replace("%", "", $taxCondition->getValue());
        $reservation_fees = (float)$this->getReservationFees();
        return $reservation_fees / 100 * $taxes;
    }

    public function totals(): array {

        return [
            'services_total' => $this->getSubTotalWithoutConditions(),
            "products_total" => $this->getProductsTotal(),
            "reservation_fees" => $this->getReservationFees(),
            'reservation_fees_include_taxes' => $this->getReservationFeesIncludeTaxes(),
            'reservation_fees_taxes' => $this->getReservationFeesTaxes(),
            "discount" => $this->discount(),
            "subtotal" => $this->getSubTotal(),
            "taxes" => $this->getConditionsByType("taxes")?->first()?->getCalculatedValue($this->getSubTotal()),
            'wallet_discount' => $this->walletDiscount(),
            'points_discount' => $this->pointsDiscount(),
            "total_without_reservation_fees_include_taxes" => $this->getTotal() - $this->getReservationFeesIncludeTaxes(),
            "total" => $this->getTotal()
        ];
    }
}
