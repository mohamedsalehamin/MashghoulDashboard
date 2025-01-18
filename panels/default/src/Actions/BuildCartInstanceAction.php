<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Exceptions\APIException;
use App\Models\ProviderOption;
use Darryldecode\Cart\CartCondition;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Lib\Cart;


class BuildCartInstanceAction {
    use AsAction;

    protected $data = [];

    public function __construct(public ProviderOption|null $providerOptions) {
        $this->providerOptions = request()->route('provider')->user?->options;
    }

    /**
     * @throws InvalidConditionException
     * @throws APIException
     */
    public function handle($request) {

        /**
         *
         * @var Cart $cart
         * */
        $cart = app('cart');
        $cart->clear();

        $this->applyServiceToCart($request, $cart);
        $cart->applyCoupon($request->get('coupon_code'));

        $cart->applyTaxes($this->getTaxesPercentageBasedOnProviderCountry($request->route('provider')));
        $this->applyWalletToCart($cart, $request);

        $this->applyReservationFeesBasedOnTerms($cart);


        return $cart;


    }

    public function isReservationFlowIsFees(): bool {
        return $this->providerOptions?->reservation_flow == "fees";
    }

    public function applyServiceToCart($request, $cart): void {
        $services = Service::findMany(request()->collect('services')->pluck("id"));
        $productPrice = 0;
        foreach ($services as $service) {

            $_service = $request
                ->collect('services')
                ->where('id', $service->id)
                ->first();

            $products = Product::where('service_id', $service->id)->whereIn("id", collect($_service['products'])->pluck('id')->toArray() ?? [])
                ->get()
                ->map(function ($product) use ($_service) {
                    return $product->setAttribute('quantity', collect($_service['products'])->where('id', $product->id)->first()['quantity'] ?? 1)
                        ->setAttribute('image', $product->getFirstMediaUrl());
                });

            $price = !$this->isReservationFlowIsFees() ? $service->price->formatByDecimal() : 0;
            $productPrice += $products->sum(fn($product) => $product->price->formatByDecimal() * $product['quantity']);
            $cart->applyItem($service, $price, 1, ['products' => $products]);
        }
        $cart->applyProducts(!$this->isReservationFlowIsFees() ? $productPrice : 0);
    }

    /**
     * @throws APIException
     */
    public function applyWalletToCart($cart, $request): void {
        if ($request->filled('wallet') && $cart->getTotal() < $request->get('wallet')) {
            throw new APIException(__("validation.api.overdue_wallet_balance"));
        }

        if ($request->filled('wallet') && auth()->user()->getTotalPointsBalance() < $request->get('wallet')) {
            throw new APIException(__("validation.api.insufficient_wallet_balance"));
        }
        if ($request->filled('wallet')) {
            $cart->applyWalletDiscount($request->get('wallet'));
        }
    }

    public function applyReservationFeesBasedOnTerms($cart): void {
        $settings = new GeneralSettings();
        if (!$settings->enabled_free_fees_in_first_reservation || auth()->user()->reservations()->count() != 0) {
            $cart->applyReservationsFees($settings?->reservations_fess ?? 0);
        }
    }

    public function getTaxesPercentageBasedOnProviderCountry($provider): int {
        return $provider?->city?->state?->country?->taxes ?? 0;
    }

}
