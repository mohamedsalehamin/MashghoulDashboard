<?php

namespace App\ProviderPanel\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use Filament\Pages\Page;

class PrivacyAndPolicyPage extends Page {
    public $page;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.content.page';

    public function getTitle(): string {
        return __('menu.privacy_policy');
    }

    public function get(): Htmlable|string {
        return __('menu.privacy_policy');
    }
    public static function getNavigationGroup(): ?string {
        return __('menu.mashghoul_pages');
    }
    public static function getNavigationLabel(): string {
        return __('menu.privacy_policy');
    }
    public function mount() {
        $setting = new  GeneralSettings;

        return $this->page = \App\ContentModule\Models\Page::find($setting->provider_pages['privacy_policy']);
    }

}
