<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Exceptions\APIException;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Lib\Cart;


class BuildCartInstanceAction {
    use AsAction;

    protected $data = [];

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
        $settings = new GeneralSettings();

        $pay_fees_mode = $settings->reservation_flow == "fees";
        $services = Service::findMany(request()->collect('services')->pluck("id"));
        $productPrice = 0;
        foreach ($services as $service) {
            $_service = $request
                ->collect('services')
                ->where('id', $service->id)
                ->first();
            $products = Product::where('service_id', $service->id)->whereIn("id", $_service['products'] ?? [])->get();
            $price = !$pay_fees_mode ? $service->price->formatByDecimal() : 0;
            $productPrice += $products->sum(fn($product) => $product->price->formatByDecimal());
            $cart->applyItem($service, $price, 1, ['products' => $products]);
        }
        $cart->applyProducts(!$pay_fees_mode ? $productPrice : 0);
        $cart->applyCoupon($request->get('coupon_code'));

        if ($settings->enabled_free_fees_in_first_reservation && auth()->user()->reservations()->count() == 0) {


        } else {
            $cart->applyReservationsFees($settings->reservations_fess);
        }

        if ($request->filled('wallet') && $cart->getTotal() < $request->get('wallet')) {
            throw new APIException(__("validation.api.overdue_wallet_balance"));
        }

        if ($request->filled('wallet') && auth()->user()->getTotalPointsBalance() < $request->get('wallet')) {
            throw new APIException(__("validation.api.insufficient_wallet_balance"));
        }
        if ($request->filled('wallet')) {
            $cart->applyWalletDiscount($request->get('wallet'));
        }


        return $cart;


    }

}
