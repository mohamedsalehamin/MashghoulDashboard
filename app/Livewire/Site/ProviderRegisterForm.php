<?php

namespace App\Livewire\Site;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use App\ContentModule\Models\Category;
use App\ContentModule\Models\ProviderActivity;
use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Enum\UserStatus;
use App\DefaultPanel\Rules\ProviderRegistrationPhoneRule;
use App\UsersModule\Models\Provider as ProviderProfile;
use App\UsersModule\Models\Users\Provider as ProviderUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProviderRegisterForm extends Component
{
    public string $phone = '';

    public string $country_code = '966';

    public string $first_name = '';

    public string $last_name = '';

    public ?string $email = null;

    public string $gender = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $salon_name = '';

    public $country_id = null;

    public $state_id = null;

    public $city_id = null;

    public $category_id = null;

    public $provider_activity_id = null;

    public bool $terms = false;

    public function mount(): void
    {
        $this->gender = GenderEnum::MALE->value ?? 'male';

        if (! session()->has('join_plan_id') || ! session()->has('join_plan_price_id')) {
            session()->flash('error', __('site.join.select_plan_first'));
            $this->redirect(route('site.join'));
        }
    }

    protected function fullPhone(): string
    {
        return '+'.$this->country_code.preg_replace('/\D/', '', $this->phone ?? '');
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:80'],
            'last_name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    (new ProviderRegistrationPhoneRule)->validate($attribute, $this->fullPhone(), $fail);
                },
            ],
            'gender' => ['required', 'in:male,female'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'salon_name' => ['required', 'string', 'min:2', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'provider_activity_id' => [
                'required',
                Rule::exists('provider_activities', 'id')->where(fn ($q) => $q->where('status', 1)),
            ],
            'terms' => ['accepted'],
        ];
    }

    public function updatedCountryId(): void
    {
        $this->state_id = null;
        $this->city_id = null;
    }

    public function updatedStateId(): void
    {
        $this->city_id = null;
    }

    public function register(): void
    {
        $this->validate();

        try {
            $phoneFormatted = phone($this->fullPhone())->formatE164();
        } catch (\Throwable $e) {
            $this->addError('phone', __('validation.api.invalid_phone_format'));

            return;
        }

        $user = DB::transaction(function () use ($phoneFormatted) {
            /** @var ProviderUser $user */
            $user = ProviderUser::create([
                'name' => trim($this->first_name.' '.$this->last_name),
                'email' => $this->email,
                'phone' => $phoneFormatted,
                'password' => $this->password,
                'gender' => $this->gender,
                'active' => UserStatus::PENDING,
                'phone_verified_at' => now(),
                'data' => [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                ],
            ]);

            $name = [
                'ar' => $this->salon_name,
                'en' => $this->salon_name,
            ];
            $bio = [
                'ar' => '—',
                'en' => '—',
            ];

            ProviderProfile::create([
                'user_id' => $user->id,
                'name' => $name,
                'bio' => $bio,
                'category_id' => $this->category_id,
                'provider_activity_id' => $this->provider_activity_id,
                'city_id' => $this->city_id,
            ]);

            return $user;
        });

        $planId = session('join_plan_id');
        $planPriceId = session('join_plan_price_id');
        $paymentMethod = session('join_payment_method', 'myfatoorah');

        session()->forget(['join_plan_id', 'join_plan_price_id', 'join_payment_method']);

        if (! $planId || ! $planPriceId) {
            session()->flash('error', __('site.join.missing_plan_session'));
            $this->redirect(route('site.join'));

            return;
        }

        Auth::guard('web')->login($user);

        $plan = Plan::query()->enabled()->with('planPrices')->find($planId);
        $planPrice = PlanPrice::find($planPriceId);

        if (! $plan || ! $planPrice || $planPrice->plan_id !== $plan->id) {
            session()->flash('error', __('site.join.invalid_plan_selection'));
            $this->redirect(route('site.join'));

            return;
        }

        $url = $plan->subscribe($planPrice, $paymentMethod);

        if ($url === '' || $url === null) {
            $subscription = $plan->subscriptions()->where('user_id', $user->id)->latest()->first();
            $tx = $subscription?->transaction;
            if ($tx && isset($tx->meta_data['invoiceURL'])) {
                $url = $tx->meta_data['invoiceURL'];
            }
        }

        if ($url === '' || $url === null) {
            session()->flash('error', __('site.join.payment_url_failed'));
            $this->redirect(route('filament.lab-panel.resources.plans.index'));

            return;
        }

        $this->redirect($url);
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

    public function getCategoriesProperty()
    {
        return Category::query()
            ->where('status', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    public function getProviderActivitiesProperty()
    {
        return ProviderActivity::query()
            ->enabled()
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    public function render()
    {
        $locale = app()->getLocale();

        return view('livewire.site.provider-register-form', [
            'countries' => $this->countries,
            'states' => $this->states,
            'cities' => $this->cities,
            'categories' => $this->categories,
            'providerActivities' => $this->providerActivities,
            'locale' => $locale,
        ]);
    }
}
