<?php

namespace App\ProviderPanel\Filament\Widgets;

use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;
use App\ProviderPanel\Filament\Pages\EditProfilePage;
use App\ProviderPanel\Filament\Resources\SeatResource;
use App\ProviderPanel\Filament\Resources\ServiceResource;
use App\UsersModule\Models\Provider;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class ProfileSetupNotice extends Widget
{
    protected static ?int $sort = -10;

    protected int | string | array $columnSpan = 'full';

    /**
     * @var view-string
     */
    protected string $view = 'provider-panel::widgets.profile-setup-notice';

    public static function canView(): bool
    {
        $provider = provider();

        if (! $provider) {
            return false;
        }

        return count(static::missingItems($provider)) > 0;
    }

    /**
     * @return list<array{key: string, label: string, url: string}>
     */
    public static function missingItems(Provider $provider): array
    {
        $panelId = Filament::getCurrentOrDefaultPanel()->getId();
        $items = [];

        if (Seat::query()->where('provider_id', $provider->id)->count() === 0) {
            $items[] = [
                'key' => 'seat',
                'label' => __('panel.profile_setup.add_first_chair'),
                'url' => SeatResource::getUrl('create', panel: $panelId),
            ];
        }

        if (Service::query()->where('provider_id', $provider->id)->count() === 0) {
            $items[] = [
                'key' => 'service',
                'label' => __('panel.profile_setup.add_first_service'),
                'url' => ServiceResource::getUrl('create', panel: $panelId),
            ];
        }

        $profileIncomplete = ! static::providerHasWorkHours($provider)
            || $provider->category_id === null
            || $provider->city_id === null;

        if ($profileIncomplete) {
            $items[] = [
                'key' => 'profile',
                'label' => __('panel.profile_setup.complete_salon_and_hours'),
                'url' => EditProfilePage::getUrl(panel: $panelId),
            ];
        }

        return $items;
    }

    protected static function providerHasWorkHours(Provider $provider): bool
    {
        return $provider->hasConfiguredWorkHours();
    }

    /**
     * @return array{items: list<array{key: string, label: string, url: string}>}
     */
    protected function getViewData(): array
    {
        $provider = provider();

        if (! $provider) {
            return ['items' => []];
        }

        return [
            'items' => static::missingItems($provider),
        ];
    }
}
