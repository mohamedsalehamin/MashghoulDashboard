<?php

namespace App\Livewire\Site;

use App\DefaultPanel\Actions\Shared\Authentication\RemoveVerficationCodes;
use App\DefaultPanel\Actions\Shared\Authentication\SendVerificationCode;
use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\IsUserNotProvider;
use App\DefaultPanel\Rules\IsValidVerificationCodeRule;
use App\UsersModule\Models\Users\Customer;
use Livewire\Component;

class LoginForm extends Component
{
    public string $phone = '';

    public string $country_code = '966';

    public string $codeString = '';

    public bool $otpSent = false;

    protected function fullPhone(): string
    {
        return '+' . $this->country_code . preg_replace('/\D/', '', $this->phone ?? '');
    }

    protected function rules(): array
    {
        if ($this->otpSent) {
            return [
                'phone' => ['required'],
                'codeString' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
            ];
        }
        return [
            'phone' => ['required', new FormatPhoneRule(), new IsUserNotProvider()],
        ];
    }

    public function sendOtp()
    {
        $this->validate([
            'phone' => [
                'required',
                function ($attr, $value, $fail) {
                    try {
                        if (! phone($this->fullPhone())->isValid()) {
                            $fail(__("validation.api.invalid_phone_format"));
                        }
                    } catch (\Throwable $e) {
                        $fail(__("validation.api.invalid_phone_format"));
                    }
                },
                function ($attr, $value, $fail) {
                    if (! (new IsUserNotProvider())->passes($attr, $this->fullPhone())) {
                        $fail((new IsUserNotProvider())->message());
                    }
                },
            ],
        ]);
        $full = $this->fullPhone();
        $customer = Customer::where('phone', $full)->first();
        SendVerificationCode::run(user: $customer, phone: $full);
        $this->otpSent = true;
        $this->dispatch('otp-sent');
    }

    public function verifyAndLogin()
    {
        $full = $this->fullPhone();
        request()->merge(['phone' => $full]);
        $this->validate([
            'phone' => ['required'],
            'codeString' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
        ]);
        $codeString = preg_replace('/\D/', '', $this->codeString);
        request()->merge(['phone' => $full]);
        if (! (new IsValidVerificationCodeRule())->passes('codeString', $codeString)) {
            $this->addError('code', __('validation.api.invalid_verification_code'));
            return;
        }
        $customer = Customer::where('phone', $full)->firstOrFail();
        RemoveVerficationCodes::run($customer);
        auth()->guard('site')->login($customer, true);
        $intended = request('intended', session('url.intended', route('site.home')));
        return $this->redirect($intended, navigate: true);
    }

    public function render()
    {
        return view('livewire.site.login-form');
    }
}
