<?php

namespace App\CatalogModule\Widgets;

use App\CatalogModule\Models\Commission;
use App\DefaultPanel\Enum\ContactSourceEnum;
use App\DefaultPanel\Enum\UserStatus;
use App\ContentModule\Models\Category;
use App\ContentModule\Models\City;
use App\ContentModule\Models\Contact;
use App\Models\User;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Lab;
use App\UsersModule\Models\User\Patient;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Cknow\Money\Money;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalOrderStats extends BaseWidget {
    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected function getStats(): array {

        return [

            Stat::make(__('panel.stats.category_count'), Category::parent()->count()),
            Stat::make(__('panel.stats.cities_count'), City::count()),
            Stat::make(__('panel.stats.administrators_count'), User::whereHas('roles', fn($q) => $q->whereNotIn('name', ['panel_user', 'customer', 'super_admin']))->count()),

            Stat::make(__('panel.stats.patients_contact_us_messages'), Contact::where('source',ContactSourceEnum::PROVIDER)->count()),

        ];
    }


}
