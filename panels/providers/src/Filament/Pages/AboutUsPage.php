<?php

namespace App\ProviderPanel\Filament\Pages;

use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use Filament\Pages\Page;

class AboutUsPage extends Page {
    public $page;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.content.page';

    public function getTitle(): string {
        return __('menu.about_us');
    }

    public function get(): \Illuminate\Contracts\Support\Htmlable|string {
        return __('menu.about_us');
    }
    public static function getNavigationGroup(): ?string {
        return __('menu.pages');
    }
    public static function getNavigationLabel(): string {
        return __('menu.about_us');
    }
    public function mount() {
        $setting = new  GeneralSettings;
        return $this->page = \App\ContentModule\Models\Page::find($setting->provider_pages['about_us']??'');
    }

}
