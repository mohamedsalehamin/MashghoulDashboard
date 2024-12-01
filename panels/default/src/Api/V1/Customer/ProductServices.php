<?php

namespace App\DefaultPanel\Api\V1\Customer;

use App\Api\Core;
use App\CatalogModule\Models\Reservation\ItemsLine;
use App\DefaultPanel\Resources\Api\Products\LightProductResource;
use App\DefaultPanel\Resources\Api\Products\ProductResource;
use App\DoctorPanel\Filament\Resources\Product;
use Tasawk\Api\Facade\Api;

class ProductServices {
    public function search(): Core {
        return Api::isOk("Search Results",
            LightProductResource::collection(
                Product::where('title->en', 'like', '%' . request()->input('term') . '%')
                    ->orWhere('title->ar', 'like', '%' . request()->input('term') . '%')
                    ->categoryEnabled()
                    ->enabled()
                    ->latest()
                    ->paginate()
            )
        );
    }

    public function index() {
        $method = request()->input('order_dir') == 'desc' ? 'sortByDesc' : 'sortBy';
        $products = Product::filtered()
            ->enabled()
            ->categoryEnabled()
            ->latest()
            ->get()
            ->when(request()->get('order_by') == 'price', fn($products) => $products->$method('sale_price'))
            ->paginate(10);
        return Api::isOk("Products List",
            LightProductResource::collection($products)
        );
    }

    public function show(Product $product) {
        return Api::isOk(__("Product information"), ProductResource::make($product));
    }

    public function toggleFavorite(Product $product): Core {
        $product->toggleFavorite();
        return Api::isOk(__("Favorites list updated"));

    }

    public function chosenForYou(): Core {
        return Api::isOk(__("Products list"), LightProductResource::collection(Product::filtered()->enabled()->chosenForYou()->paginate()));
    }

    public function dealOfTheDay(): Core {
        return Api::isOk(__("Products list"), LightProductResource::collection(Product::filtered()->enabled()->dealOfTheDay()->paginate()));
    }

    public function latestOrderedProducts(): Core {
        $products = ItemsLine::whereHas('order',fn($q)=>$q->where('customer_id',auth()->id()))->latest("id")->get()->pluck('model.id');
        return Api::isOk(__("Products list"), LightProductResource::collection(Product::whereIn('id',$products)->filtered()->paginate()));
    }


}
