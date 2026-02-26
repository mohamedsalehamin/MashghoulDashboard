<?php

namespace App\UsersModule\Resources\ProviderResource\Widgets;

use App\CatalogModule\Models\Commission;
use App\DefaultPanel\Enum\ContactSourceEnum;
use App\DefaultPanel\Enum\UserStatus;
use App\ContentModule\Models\Category;
use App\ContentModule\Models\City;
use App\ContentModule\Models\Contact;
use App\Models\User;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Lab;
use App\UsersModule\Models\Provider;
use App\UsersModule\Models\User\Patient;
use App\UsersModule\Models\Users\Customer;
use Cknow\Money\Money;
use DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Theamostafa\Wallet\Models\Wallet;

class WalletStats extends BaseWidget {

    public $record;

    protected function getStats(): array {


        return [

            Stat::make(__('panel.stats.balance'), Money::parse($this->record?->provider?->wallet?->balance??0)->format()),

        ];
    }


}
