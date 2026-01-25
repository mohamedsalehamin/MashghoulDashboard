<?php

namespace App\ProviderPanel\Filament\Widgets;

use Filament\Widgets\Widget;

class ProfileUrlWidget extends Widget
{
    protected string $view = 'provider-panel::widgets.profile-url-widget';

    public function getProfileUrl(): string
    {
        $provider = auth()->user()->provider;
        if (!$provider) {
            return '';
        }
        return route('site.share_provider', str_replace(" ", "&", $provider->getTranslation('name', 'en') ?? $provider->name));
    }
} 