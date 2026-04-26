<?php

namespace App\Livewire\Site;

use App\DefaultPanel\Actions\Customer\RegisterCustomer;
use App\DefaultPanel\Actions\Shared\Authentication\RemoveVerficationCodes;
use App\DefaultPanel\Actions\Shared\Authentication\SendVerificationCode;
use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\IsValidVerificationCodeRule;
use App\UsersModule\Models\Users\Customer;
use Livewire\Component;

class CustomerRegisterForm extends Component
{
    public string $phone = '';

    public string $country_code = '966';

    public string $codeString = '';

    public string $first_name = '';

    public string $last_name = '';

    public ?string $email = null;

    public string $gender = '';

    public $country_id = null;

    public $state_id = null;

    public $city_id = null;

    public bool $otpSent = false;

    public bool $otpVerified = false;

    public function mount()
    {
        $this->gender = GenderEnum::MALE->value ?? 'male';

        // Prefill from login redirect: /register?phone=5xxxxxxx&country_code=966&code=1234
        $qPhone = request()->query('phone');
        $qCountryCode = request()->query('country_code');
        $qCode = request()->query('code');

        if (is_string($qCountryCode) && $qCountryCode !== '') {
            $this->country_code = preg_replace('/\D/', '', $qCountryCode) ?: $this->country_code;
        }

        if (is_string($qPhone) && $qPhone !== '') {
            $this->phone = preg_replace('/\D/', '', $qPhone) ?: $this->phone;
        }

        if (is_string($qCode) && $qCode !== '') {
            $this->codeString = preg_replace('/\D/', '', $qCode);
            $this->otpSent = true;

            // If the code is still valid, jump directly to the profile step.
            request()->merge(['phone' => $this->fullPhone()]);
            if ((new IsValidVerificationCodeRule())->passes('codeString', $this->codeString)) {
                $this->otpVerified = true;
            }
        }
    }

    protected function fullPhone(): string
    {
        return '+' . $this->country_code . preg_replace('/\D/', '', $this->phone ?? '');
    }

    protected function rules(): array
    {
        if ($this->otpVerified) {
            return [
                'phone' => ['required'],
                'codeString' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
                'first_name' => ['required', 'string', 'min:3', 'max:40'],
                'last_name' => ['required', 'string', 'min:3', 'max:40'],
                'email' => ['nullable', 'email', 'unique:users'],
                'gender' => ['required', 'in:male,female'],
                'country_id' => ['required', 'exists:countries,id'],
                'state_id' => ['required', 'exists:states,id'],
                'city_id' => ['required', 'exists:cities,id'],
            ];
        }
        if ($this->otpSent) {
            return [
                'phone' => ['required'],
                'codeString' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
            ];
        }
        return [
            'phone' => [
                'required',
                function ($attr, $value, $fail) {
                    if (Customer::where('phone', $this->fullPhone())->exists()) {
                        $fail(__('validation.unique', ['attribute' => __('site.fields.phone')]));
                    }
                },
                function ($attr, $value, $fail) {
                    try {
                        if (! phone($this->fullPhone())->isValid()) {
                            $fail(__('validation.api.invalid_phone_format'));
                        }
                    } catch (\Throwable $e) {
                        $fail(__('validation.api.invalid_phone_format'));
                    }
                },
            ],
        ];
    }

    public function sendOtp()
    {
        $this->validate([
            'phone' => [
                'required',
                function ($attr, $value, $fail) {
                    if (Customer::where('phone', $this->fullPhone())->exists()) {
                        $fail(__('validation.unique', ['attribute' => __('site.fields.phone')]));
                    }
                },
                function ($attr, $value, $fail) {
                    try {
                        if (! phone($this->fullPhone())->isValid()) {
                            $fail(__('validation.api.invalid_phone_format'));
                        }
                    } catch (\Throwable $e) {
                        $fail(__('validation.api.invalid_phone_format'));
                    }
                },
            ],
        ]);
        SendVerificationCode::run(phone: $this->fullPhone());
        $this->otpSent = true;
        $this->dispatch('otp-sent');
    }

    public function verifyOtp()
    {
        $this->validate([
            'phone' => ['required'],
            'codeString' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
        ]);
        $codeString = preg_replace('/\D/', '', $this->codeString);
        request()->merge(['phone' => $this->fullPhone()]);
        if (! (new IsValidVerificationCodeRule())->passes('codeString', $codeString)) {
            $this->addError('code', __('validation.api.invalid_verification_code'));

            return;
        }
        $this->otpVerified = true;
    }

    public function register()
    {
        $this->validate([
            'phone' => ['required'],
            'codeString' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
            'first_name' => ['required', 'string', 'min:3', 'max:40'],
            'last_name' => ['required', 'string', 'min:3', 'max:40'],
            'email' => ['nullable', 'email', 'unique:users'],
            'gender' => ['required', 'in:male,female'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
        ]);
        $codeString = preg_replace('/\D/', '', $this->codeString);
        request()->merge(['phone' => $this->fullPhone()]);
        if (! (new IsValidVerificationCodeRule())->passes('codeString', $codeString)) {
            $this->addError('code', __('validation.api.invalid_verification_code'));

            return;
        }
        $customer = RegisterCustomer::run(
            $this->first_name,
            $this->last_name,
            $this->fullPhone(),
            $this->city_id,
            $this->gender,
            $this->email,
            null
        );
        RemoveVerficationCodes::run($customer);
        auth()->guard('site')->login($customer, true);

        return $this->redirect(route('site.register.success'), navigate: true);
    }

    public function updatedCountryId()
    {
        $this->state_id = null;
        $this->city_id = null;
    }

    public function updatedStateId()
    {
        $this->city_id = null;
    }

    public function getCountriesProperty()
    {
        return \App\ContentModule\Models\Country::enabled()->orderBy('name')->get();
    }

    public function getStatesProperty()
    {
        if (! $this->country_id) {
            return collect();
        }

        return \App\ContentModule\Models\State::where('country_id', $this->country_id)->enabled()->orderBy('name')->get();
    }

    public function getCitiesProperty()
    {
        if (! $this->state_id) {
            return collect();
        }

        return \App\ContentModule\Models\City::where('state_id', $this->state_id)->enabled()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.site.customer-register-form', [
            'countries' => $this->countries,
            'states' => $this->states,
            'cities' => $this->cities,
        ]);
    }
}
