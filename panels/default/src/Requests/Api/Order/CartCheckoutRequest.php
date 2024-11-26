<?php

namespace App\DefaultPanel\Requests\Api\Order;

use App\CatalogModule\Models\Branch;
use App\DoctorPanel\Filament\Resources\Product;
use App\CrmModule\Models\AddressBook;
use App\DefaultPanel\Enum\DeliveryMethods;
use App\DefaultPanel\Rules\AddressBelongToAuthUserRule;
use App\DefaultPanel\Rules\IsRequiredProductOptionsRepresentRule;
use App\DefaultPanel\Rules\IsValidCoupon;
use App\DefaultPanel\Rules\IsValidProductOptionsRule;
use App\DefaultPanel\Rules\IsValidProductOptionValuesRule;
use Carbon\Carbon;
use GeometryLibrary\PolyUtil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;


class CartCheckoutRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    public function authorize() {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            'products' => ['required', 'array', function ($attribute, $value, $fail) {
                $products = Product::findMany(collect($value)->pluck('id'));
                $products->each(function ($product, $index) use ($fail) {
                    if ($product->quantity < $this->input("products.$index.quantity")) {
                        $fail(__('panel.messages.product_not_available', ['product' => $product->title]));
                    }
                });
            }],
            'products.*.id' => ['bail', 'required', Rule::exists('products', 'id')->where('status', 1), new IsRequiredProductOptionsRepresentRule()],
            'products.*.quantity' => ['required', 'numeric', 'min:1',],
            'products.*.options.*.id' => [new IsValidProductOptionsRule()],
            'products.*.options.*.value_id' => [new IsValidProductOptionValuesRule()],
            'coupon_code' => ['nullable', 'exists:coupons,code', new IsValidCoupon()],
            "delivery_method" => ['required', Rule::enum(DeliveryMethods::class)],
            'payment_gateway' => ['required', 'in:myfatoorah,cash'],
            "address_id" => ['bail', Rule::exists('address_books', 'id'), Rule::requiredIf(in_array($this->get('delivery_method'), [DeliveryMethods::DELIVERY->value, DeliveryMethods::SUPER_DELIVERY->value])), new AddressBelongToAuthUserRule, function ($attribute, $value, $fail) {
                $address = $this->getAddress();
                $lat = $address->map_location['lat'];
                $lng = $address->map_location['lng'];

                $coordinate = collect($address->district->boundaries->getCoordinates())->map(fn($coordinate) => collect($coordinate)->map(fn($c) => ['lat' => $c[1], 'lng' => $c[0]])->toArray())->toArray()[0];

                if ($address->district->boundaries && !PolyUtil::containsLocation(['lat' => $lat, 'lng' => $lng], $coordinate)) {
                    $fail(__('panel.messages.address_not_in_branch_boundaries'));
                }
                if (!$this->getClosestBranch($lat, $lng)) {
                    $fail(__('panel.messages.no_branch_nearest_to_address'));
                }
            }],
            'branch_id' => [Rule::requiredIf($this->get('delivery_method') == 'takeaway'), function ($attribute, $value, $fail) {
                if ($this->get('delivery_method') == 'takeaway' && Branch::find($value)->takeaway_mode == 0) {
                    return $fail(__('validation.api.current_branch_cant_provide_takeaway_method'));
                }
            }],
            'period' => ['required', 'in:0,1'],
            'date' => ['required', 'date', 'after_or_equal::' . now()->format("Y-m-d"), function ($attribute, $value, $fail) {

                $day_name = strtolower(Carbon::parse($value)->locale('en')->dayName);
                $one = $this->input('delivery_method') == 'super_delivery' ? 'delivery' : $this->get('delivery_method');
                $branch = $this->getBranch();
                if (!isset($branch->working_days[$one][$this->get('period')])) {
                    $extra = request()->has('debug') ? $this->getBranch()->id : '';
                    return $fail(__('panel.messages.no_branch_not_available_now') . $extra);
                }
                if (!collect($branch
                    ->working_days[$one][$this->get('period')])
                    ->where('day_name', $day_name)
                    ->where('status', 1)
                    ->count()) {
                    $fail(__('panel.messages.no_working_times_available', ['day' => $day_name]));
                }


            }],];

    }

    protected
    function passedValidation(): void {
        $address = $this->getAddress();
        if (in_array($this->get('delivery_method'), ['delivery', 'super_delivery'])) {
            $this->merge([
                'branch_id' => $this->getClosestBranch($address?->map_location['lat'], $address?->map_location['lng'])?->id
            ]);
        }
    }

    public
    function getAddress() {
        return AddressBook::find($this->get('address_id'));
    }

    public
    function getClosestBranch($lat, $lng) {
        return Branch::enabled()
            ->withDistanceSphere('location', new Point($lat, $lng))
            ->whereContains('boundaries', new Point($lat, $lng))
            ->when($this->get('delivery_method') == 'delivery', fn($query) => $query->where('takeaway_mode', 1))
            ->first();
    }

    public
    function getBranch() {
        $address = $this->getAddress();
        $lat = $address?->map_location['lat'];
        $lng = $address?->map_location['lng'];

        return in_array($this->get('delivery_method'), ['delivery', 'super_delivery'])
            ? $this->getClosestBranch($lat, $lng)
            : Branch::find($this->get('branch_id'));
    }

}
