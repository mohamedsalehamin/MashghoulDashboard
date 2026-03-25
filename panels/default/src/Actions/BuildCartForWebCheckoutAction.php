<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Lib\Cart;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Exceptions\APIException;
use App\DefaultPanel\Enum\WalletWithdrawEnum;
use App\UsersModule\Models\Provider;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BuildCartForWebCheckoutAction
{
    /**
     * Build cart from services array (for web checkout when route provider may not exist).
     *
     * @param  array{seat_id: int, services: array, coupon_code?: string, wallet?: string, points?: string}  $data
     * @throws InvalidConditionException
     * @throws APIException
     */
    public function handle(Provider $provider, array $data): Cart
    {
        $cart = app('cart');
        $cart->clear();
        session(['cart_provider_id' => $provider->id]);

        $services = collect($data['services'] ?? []);
        $this->applyServicesToCart($services, $cart);
        $cart->applyTaxes($provider->city?->state?->country?->taxes ?? 0);
        $this->applyWalletToCart($cart, $data);
        $this->applyPointsToCart($cart, $data);
        $this->applyReservationFees($cart);
        if (!empty($data['coupon_code'])) {
            $cart->applyCoupon($data['coupon_code']);
        }

        return $cart;
    }

    protected function applyServicesToCart(Collection $servicesData, Cart $cart): void
    {
        $services = Service::findMany($servicesData->pluck('id'));
        $productPrice = 0;
        foreach ($services as $service) {
            $data = $servicesData->firstWhere('id', $service->id);
            $productsInput = collect($data['products'] ?? []);
            $products = Product::where('service_id', $service->id)
                ->whereIn('id', $productsInput->pluck('id')->filter()->toArray())
                ->get()
                ->map(function ($product) use ($productsInput) {
                    $qty = (int) ($productsInput->firstWhere('id', $product->id)['quantity'] ?? 1);
                    return $product->setAttribute('quantity', max(1, $qty))
                        ->setAttribute('image', $product->getFirstMediaUrl());
                });
            $price = $service->sale_price?->getAmount() > 0 && $service->sale_price?->getAmount() < $service->price?->getAmount()
                ? $service->sale_price->formatByDecimal()
                : $service->price->formatByDecimal();
            $productPrice += $products->sum(fn ($p): float => ($p->sale_price?->getAmount() > 0 ? $p->sale_price->formatByDecimal() : $p->price->formatByDecimal()) * $p['quantity']);
            $cart->applyItem($service, $price, 1, ['products' => $products]);
        }
        if ($productPrice > 0) {
            $cart->applyProducts($productPrice);
        }
    }

    protected function applyWalletToCart(Cart $cart, array $data): void
    {
        $wallet = $data['wallet'] ?? '';
        if (empty($wallet)) {
            return;
        }
        $user = auth()->user();
        $pendingWithdrawalAmount = $user->withdrawalRequests()
            ->whereIn('status', [WalletWithdrawEnum::PENDING, WalletWithdrawEnum::WAITING_TRANSFER])
            ->sum('amount');
        $availableBalance = $user->balance - $pendingWithdrawalAmount;
        if ($cart->getTotal() < (float) $wallet) {
            throw new APIException(__('validation.api.overdue_wallet_balance'));
        }
        if ($availableBalance < (float) $wallet) {
            throw new APIException(__('validation.api.insufficient_wallet_balance'));
        }
        $cart->applyWalletDiscount($wallet);
    }

    protected function applyPointsToCart(Cart $cart, array $data): void
    {
        $points = $data['points'] ?? '';
        if (empty($points)) {
            return;
        }
        $user = auth()->user();
        if ($cart->getTotal() < (float) $points) {
            throw new APIException(__('validation.api.overdue_points_balance'));
        }
        if ($user->getTotalPointsBalance() < (float) $points) {
            throw new APIException(__('validation.api.insufficient_points_balance'));
        }
        $cart->applyPointsDiscount($points);
    }

    protected function applyReservationFees(Cart $cart): void
    {
        $settings = new GeneralSettings();
        if (!$settings->enabled_free_fees_in_first_reservation || auth()->user()->reservations()->count() != 0) {
            $cart->applyReservationsFees($settings?->reservations_fess ?? 0);
        }
    }
}
