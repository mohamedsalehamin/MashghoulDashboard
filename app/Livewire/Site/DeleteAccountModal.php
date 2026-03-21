<?php

namespace App\Livewire\Site;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteAccountModal extends Component
{
    public function deleteAccount()
    {
        $user = auth()->guard('site')->user();
        Auth::guard('site')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        $user->delete();

        return $this->redirect(route('site.home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.site.delete-account-modal');
    }
}
