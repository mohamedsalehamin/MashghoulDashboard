<?php

namespace App\ProviderPanel\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;
use App\ContentModule\Models\Faq;
use App\DefaultPanel\Enum\FaqLocationEnum;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use Filament\Pages\Page;

class FaqsPage extends Page {
    public $faqs;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.content.faqs';

    public function getTitle(): string {
        return __('menu.faqs');
    }

    public function get(): Htmlable|string {
        return __('menu.faqs');
    }
    public static function getNavigationGroup(): ?string {
        return __('menu.mashghoul_pages');
    }
    public static function getNavigationLabel(): string {
        return __('menu.faqs');
    }
    public function mount() {

        return $this->faqs = Faq::get();
    }

}
