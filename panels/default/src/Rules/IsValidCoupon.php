<?php

namespace App\DefaultPanel\Rules;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use App\ContentModule\Models\Coupon;
use App\DefaultPanel\Settings\GeneralSettings;
use Illuminate\Contracts\Validation\Rule;

class IsValidCoupon implements Rule {
    protected string $message = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $valueCheckCoupon
     * @return bool
     */
    public function passes($attribute, $value) {
        $cp = Coupon::where('code', $value)->first();

        if (!$cp) {
            $this->message = (__("validation.api.coupon_code_not_found"));
            return false;
        }
        $auth_user = request()->user('sanctum');

        if ($cp?->isUserExceedUsageTimes($auth_user)) {
            $this->message = (__("validation.api.coupon_code_exceeds_the_number_of_usages_times"));
            return false;
        }
        if (!$cp->isAvailableToUse()) {
            $this->message = (__("validation.api.coupon_code_is_expired"));
            return false;
        }

        $provider = request()->route('provider');
        $providerId = is_object($provider) ? ($provider->id ?? null) : $provider;
        if ($cp->scope === Coupon::SCOPE_PROVIDERS) {
            if (empty($providerId) || ! $cp->providers()->where('providers.id', $providerId)->exists()) {
                $this->message = (__("validation.api.coupon_not_valid_for_this_provider"));
                return false;
            }
        }

        if ($cp->requested_by === Coupon::REQUESTED_BY_PROVIDER) {
            // For provider-requested coupons, enforce provider context when possible.
            if (empty($providerId)) {
                $this->message = (__("validation.api.coupon_code_not_found"));
                return false;
            }
            if (!empty($cp->provider_id) && (int) $cp->provider_id !== (int) $providerId) {
                $this->message = (__("validation.api.coupon_not_valid_for_this_provider"));
                return false;
            }
        }

        $servicesInput = collect(request()->input('services', []));
        $serviceIds = $servicesInput->pluck('id')->filter()->map(fn ($v) => (int) $v)->values();
        $services = Service::findMany($serviceIds);

        $servicesBase = 0.0;
        $servicesBaseNoSale = 0.0;
        $productsBase = 0.0;
        $productsBaseNoSale = 0.0;

        foreach ($services as $service) {
            $serviceRow = $servicesInput->firstWhere('id', $service->id) ?? [];
            $serviceOnSale = $service->sale_price?->getAmount() > 0 && $service->sale_price?->getAmount() < $service->price?->getAmount();
            $serviceEffective = $serviceOnSale ? (float) $service->sale_price->formatByDecimal() : (float) $service->price->formatByDecimal();

            $servicesBase += $serviceEffective;
            if (!$serviceOnSale) {
                $servicesBaseNoSale += $serviceEffective;
            }

            $productsInput = collect($serviceRow['products'] ?? []);
            $productIds = $productsInput->pluck('id')->filter()->map(fn ($v) => (int) $v)->values();
            if ($productIds->isEmpty()) {
                continue;
            }
            $products = Product::where('service_id', $service->id)->whereIn('id', $productIds)->get();
            foreach ($products as $product) {
                $qty = (int) ($productsInput->firstWhere('id', $product->id)['quantity'] ?? 1);
                $qty = max(1, $qty);
                $productOnSale = $product->sale_price?->getAmount() > 0 && $product->sale_price?->getAmount() < $product->price?->getAmount();
                $productEffective = $productOnSale ? (float) $product->sale_price->formatByDecimal() : (float) $product->price->formatByDecimal();
                $line = $productEffective * $qty;
                $productsBase += $line;
                if (!$productOnSale) {
                    $productsBaseNoSale += $line;
                }
            }
        }

        // Fees must NOT be part of provider discount %/fixed base (same as Cart::applyCoupon) — that caused
        // confusing amounts vs "إجمالي الخدمات". Fees are only included below for min_order cart_total checks.
        $feesBase = $this->reservationFeesBaseForValidation();
        $cartTotalBase = $servicesBase + $productsBase + $feesBase;

        if ($cp->requested_by === Coupon::REQUESTED_BY_ADMIN) {
            $eligibleBase = $feesBase;
        } else {
            $applyTarget = $cp->apply_target ?: Coupon::APPLY_TARGET_ALL_ITEMS;
            $eligibleBase = match ($applyTarget) {
                Coupon::APPLY_TARGET_SERVICES_ONLY => $servicesBase,
                Coupon::APPLY_TARGET_SERVICES_WITHOUT_DISCOUNT => $servicesBaseNoSale,
                Coupon::APPLY_TARGET_PRODUCTS_ONLY => $productsBase,
                Coupon::APPLY_TARGET_PRODUCTS_WITHOUT_DISCOUNT => $productsBaseNoSale,
                Coupon::APPLY_TARGET_ALL_ITEMS_WITHOUT_DISCOUNT => ($servicesBaseNoSale + $productsBaseNoSale),
                default => ($servicesBase + $productsBase),
            };
        }

        if ($eligibleBase <= 0) {
            $this->message = $cp->messageWhenEligibleBaseIsZero();
            return false;
        }

        $min = (float) ($cp->meta_data['min_order_value'] ?? 0);
        if ($min > 0) {
            $type = (string) ($cp->meta_data['min_order_value_type'] ?? 'cart_total');
            // cart_total → services + products + reservation fee; eligible_base → discount base only (no fee).
            $baseForMin = $type === 'eligible_base' ? (float) $eligibleBase : (float) $cartTotalBase;
            if ($baseForMin < $min) {
                $this->message = (__("validation.api.coupon_code_min_order_value", ['value' => $min]));
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->message;
    }

    /**
     * Match BuildCartInstanceAction::applyReservationFeesBasedOnTerms (fee amount or zero for first free booking).
     */
    private function reservationFeesBaseForValidation(): float {
        $settings = new GeneralSettings();
        $user = auth()->user() ?? request()->user('sanctum');
        if ($settings->enabled_free_fees_in_first_reservation && $user && $user->reservations()->count() === 0) {
            return 0.0;
        }

        return (float) ($settings->reservations_fess ?? 0);
    }
}
