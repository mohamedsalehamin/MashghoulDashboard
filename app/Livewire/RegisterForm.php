<?php

namespace App\Livewire;

use App\ContentModule\Models\JoinRequest;
use Livewire\Attributes\Rule;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class RegisterForm extends ModalComponent {
    #[Rule(['required', 'string'])]
    public $first_name;
    #[Rule(['required', 'string'])]
    public $last_name;
    #[Rule(['required', 'email'])]
    public $email;
    #[Rule(['required'])]
    public $phone;
    #[Rule(['required'])]
    public $gender;
    #[Rule(['required', 'string', 'confirmed'])]
    public $password;
    public $password_confirmation;
    #[Rule(['accepted'])]
    public $terms;

    public function render() {
        return view('site.components.livewire.register-form');
    }

    public function submit() {

        $this->validate();
        JoinRequest::create($this->except(''));
        session()->flash('message', __("site.heading.register_successfully"));

        return redirect()->route('site.pages.register');
    }
}
