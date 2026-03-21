<?php

namespace App\Http\Responses\Auth;

use Filament\Facades\Filament;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class PanelLoginResponse implements LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        // Always redirect to the panel dashboard, ignoring url.intended from site routes
        // to prevent redirect to set-location after admin/portal login
        return redirect()->to(Filament::getUrl());
    }
}
