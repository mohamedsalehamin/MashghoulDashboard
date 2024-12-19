<?php

namespace App\Livewire;

use Livewire\Component;

class RegisterButton extends Component {
    public function render() {
        return view('site.components.livewire.register-button');
    }

    public function openModal() {

        $this->dispatch('openModal', 'register-model');
    }
}
