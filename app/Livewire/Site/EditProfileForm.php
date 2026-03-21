<?php

namespace App\Livewire\Site;

use App\ContentModule\Models\City;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
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
        if (preg_match('/^\+(\d{1,4})(\d+)$/', $fullPhone, $m)) {
            $this->country_code = $m[1];
            $this->phone = $m[2];
        } else {
            $this->phone = preg_replace('/\D/', '', $fullPhone);
        }
        $this->gender = $user->gender ?? '';
        $this->city_id = $user->city_id;
        $this->region_id = $user->city?->state_id;
        $this->country_id = $user->city?->state?->country_id;
    }

    protected function fullPhone(): string
    {
        return '+' . $this->country_code . preg_replace('/\D/', '', $this->phone ?? '');
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'gender' => ['nullable', 'in:male,female'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'region_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = site()->user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->fullPhone(),
            'gender' => $this->gender ?: $user->gender,
            'city_id' => $this->city_id,
        ]);

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
