<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Product;
use Lorisleiva\Actions\Concerns\AsAction;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Lib\Cart;
use App\DefaultPanel\Settings\GeneralSettings;
use App\UsersModule\Models\Provider;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Illuminate\Support\Collection;

class AddProviderCartAction
{
    use AsAction;
    /**
     * Add selected services (and optional products) to cart for a provider.
     * Clears cart first, then adds items. Stores cart_provider_id in session.
     *
     * @param  array{seat_id: int, services: array<array{id: int, products?: array<array{id: int, quantity?: int}>}>}  $data
     * @throws InvalidConditionException
     */
    public function handle(Provider $provider, array $data): Cart
    {
        /** @var Cart $cart */
        $cart = app('cart');
        $cart->clear();

        $services = $this->normalizeServices($data['services']);
        $this->applyServicesToCart($services, $cart);

        $cart->applyTaxes($this->getTaxesPercentage($provider));
        $this->applyReservationFees($cart);

        session(['cart_provider_id' => $provider->id]);

        return $cart;
    }

    protected function normalizeServices(array $services): Collection
    {
        return collect($services)->map(function ($s) {
            $id = is_array($s) ? ($s['id'] ?? $s) : $s;
            $products = isset($s['products']) ? collect($s['products']) : collect();
            return ['id' => (int) $id, 'products' => $products];
        });
    }

    protected function applyServicesToCart(Collection $servicesData, Cart $cart): void
    {
        $services = Service::findMany($servicesData->pluck('id'));
        $productPrice = 0;

        foreach ($services as $service) {
            $data = $servicesData->firstWhere('id', $service->id);
            $productsInput = $data['products'] ?? collect();

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

    protected function getTaxesPercentage(Provider $provider): int
    {
        return (int) ($provider->city?->state?->country?->taxes ?? 0);
    }

    protected function applyReservationFees(Cart $cart): void
    {
        $settings = new GeneralSettings();
        $fees = $settings->reservations_fess ?? 0;
        if ($fees > 0) {
            $cart->applyReservationsFees($fees);
        }
    }
}
