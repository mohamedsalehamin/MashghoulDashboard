<?php

namespace App\Livewire\Site;

use App\ContentModule\Models\City;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditProfileForm extends Component
{
    use WithFileUploads;

    public $name = '';

    public $email = '';

    public string $phone = '';

    public string $country_code = '966';

    public $gender = '';

    public ?string $dob = null;

    public $country_id = null;

    public $region_id = null;

    public $city_id = null;
    public $avatar = null;
    public $showSuccessModal = false;

    public function mount()
    {
        $user = site()->user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $fullPhone = $user->phone ?? '';
        // Parse E.164 safely.
        // Important: avoid greedy capture that can swallow the first national digit
        // (e.g. +9665xxxxxxxx could be parsed as country=9665, phone=xxxxxxxx).
        if (preg_match('/^\+966(\d+)$/', $fullPhone, $m)) {
            $this->country_code = '966';
            $this->phone = $m[1];
        } elseif (preg_match('/^\+(\d{1,3})(\d+)$/', $fullPhone, $m)) {
            $this->country_code = $m[1];
            $this->phone = $m[2];
        } else {
            $this->phone = preg_replace('/\D/', '', $fullPhone);
        }

        // UX: Saudi mobile numbers are commonly written with a leading trunk "0" (05xxxxxxxx).
        // We store E.164 (+9665xxxxxxxx) but show the familiar local form in the edit field.
        // if ($this->country_code === '966') {
        //     $digits = preg_replace('/\D/', '', $this->phone ?? '');
        //     if ($digits !== '' && $digits[0] !== '0') {
        //         $this->phone = '0' . $digits;
        //     } else {
        //         $this->phone = $digits;
        //     }
        // }

        $this->gender = $user->gender ?? '';
        $this->city_id = $user->city_id;
        $this->region_id = $user->city?->state_id;
        $this->country_id = $user->city?->state?->country_id;
        $this->dob = $user->dob ? Carbon::parse($user->dob)->toDateString() : null;
    }

    protected function fullPhone(): string
    {
        $digits = preg_replace('/\D/', '', $this->phone ?? '');
        // dd($digits);
        // If user enters local form starting with 0, drop a single trunk prefix.
        // Example: 05xxxxxxxx -> +9665xxxxxxxx
        if ($digits !== '' && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }
        return '+' . $this->country_code . $digits;
    }

    public function save()
    {
        $user = site()->user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'gender' => ['nullable', 'in:male,female'],
            'dob' => $user->dob ? ['nullable'] : ['nullable', 'date', 'before:today'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'region_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->fullPhone(),
            'gender' => $this->gender ?: $user->gender,
            'city_id' => $this->city_id,
        ];

        // DOB is set-once (matches app behavior): allow setting only if currently empty.
        if (!$user->dob && $this->dob) {
            $payload['dob'] = $this->dob;
        }

        $user->update($payload);

        if ($this->avatar) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($this->avatar->getRealPath())
                ->usingFileName($this->avatar->getClientOriginalName())
                ->toMediaCollection('avatar');
        }

        $this->avatar = null;
        $this->showSuccessModal = true;
        $this->dispatch('profile-updated');
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
    }

    public function updatedCountryId()
    {
        $this->region_id = null;
        $this->city_id = null;
    }

    public function updatedRegionId()
    {
        $this->city_id = null;
    }

    public function render()
    {
        $user = site()->user();
        $locale = app()->getLocale();
        $countries = Country::enabled()->orderBy('name')->get();
        $regions = $this->country_id
            ? State::where('country_id', $this->country_id)->enabled()->orderBy('name')->get()
            : collect();
        $stateId = $this->region_id ?? $user->city?->state_id;
        $cities = $stateId
            ? City::where('state_id', $stateId)->enabled()->orderBy('name')->get()
            : collect();

        return view('livewire.site.edit-profile-form', [
            'user' => $user,
            'countries' => $countries,
            'regions' => $regions,
            'cities' => $cities,
            'locale' => $locale,
        ]);
    }
}
