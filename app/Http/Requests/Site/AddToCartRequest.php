<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $provider = $this->route('provider');
        $providerId = is_object($provider) && method_exists($provider, 'getKey') ? $provider->getKey() : (int) $provider;
        $seatId = $this->input('seat_id');

        return [
            'seat_id' => [
                'required',
                Rule::exists('seats', 'id')->where('provider_id', $providerId),
            ],
            'services' => ['required', 'array', 'min:1'],
            'services.*.id' => [
                'required',
                Rule::exists('seat_service', 'service_id')->where('seat_id', $seatId),
            ],
            'services.*.products' => ['nullable', 'array'],
            'services.*.products.*.id' => ['required', 'exists:products,id'],
            'services.*.products.*.quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
