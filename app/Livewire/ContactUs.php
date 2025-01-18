<?php

namespace App\Livewire;

use App\ContentModule\Models\Contact;
use App\ContentModule\Models\ContactType;
use Livewire\Component;


class ContactUs extends Component {
    public $name;
    public $email;
    public $phone;
    public $contact_type_id;
    public $title;
    public $message;
    public $types;
    public $successMessage;

    public function getRules() {
        return [
            'name' => ['required', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required'],
            'contact_type_id' => ['required'],
            'title' => ['required', 'min:3', 'max:255'],
            'message' => ['required', 'min:3', 'max:255'],
        ];
    }



    public function mount() {
        $this->types = ContactType::latest()->get();

    }

    public function updated($propertyName) {
        $this->validateOnly($propertyName);
    }

    public function save(): void {
        $contact = $this->validate();

        Contact::create($this->all());
        $this->resetExcept('types');
        $this->dispatch('resetContactForm');
        $this->successMessage = __('site.contact_us_success');

    }


    public function render() {
        return view('livewire.contact-us', [
            'types' => $this->types,
        ]);
    }
}
