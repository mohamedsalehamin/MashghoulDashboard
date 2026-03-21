<?php

namespace App\Livewire\Site;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LogoutModal extends Component
{
    public function logout()
    {
        Auth::guard('site')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect(route('site.home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.site.logout-modal');
    }
}
