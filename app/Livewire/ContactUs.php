<?php

namespace App\Livewire;

use App\ContentModule\Models\Contact;
use App\ContentModule\Models\ContactType;
use Livewire\Component;


class ContactUs extends Component
{
    public $name;
    public $email;
    public $phone;
    public $country_code = '966';
    public $contact_type_id;
    public $title;
    public $message;
    public $types;
    public $successMessage;

    public function getRules()
    {
        $rules = [
            'name' => ['required', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required'],
            'title' => ['required', 'min:3', 'max:255'],
            'message' => ['required', 'min:25', 'max:2000'],
        ];
        $types = $this->types ?? collect();
        if ($types->isNotEmpty()) {
            $rules['contact_type_id'] = ['nullable', 'exists:contact_types,id'];
        }
        return $rules;
    }

    public function rules(): array
    {
        return $this->getRules();
    }

    public function mount()
    {
        $this->types = ContactType::enabled()->get();

    }

    public function updated($propertyName) {
        $this->validateOnly($propertyName);
    }

    public function save(): void
    {
        $this->validate();

        $fullPhone = '+' . $this->country_code . preg_replace('/\D/', '', $this->phone ?? '');
        Contact::create($this->only(['name', 'email', 'contact_type_id', 'title', 'message']) + ['phone' => $fullPhone]);
        $this->resetExcept('types');
        $this->dispatch('resetContactForm');
        $this->successMessage = __('site.contact_us_success');

    }


    public function render()
    {
        return view('livewire.contact-form', [
            'types' => $this->types,
        ]);
    }
}
