<?php

namespace App\DefaultPanel\Settings;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Get;
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings {
    public string|null $app_logo;
    public string $app_name;
    public string $app_email;
    public string $app_phone;
    public string $reservations_fess;
    public string $app_percentage;
    public string $reservation_flow;
    public string $enabled_free_fees_in_first_reservation;
    public array $points;
    public string $app_whatsapp;
    public string $app_address;
    public float $taxes;
    public array $applications_links = [];
    public array $app_pages = [];
    public array $provider_pages = [];

    public array $social_links = [];
    public array $working_days = [];

    public static function group(): string {
        return 'general';
    }

    public static function getDayTimesSlot($from = '00:00', $to = '23:59') {
        $startPeriod = Carbon::parse($from);
        $endPeriod = Carbon::parse($to);
        $interval = "60 minutes";
        $period = CarbonPeriod::create($startPeriod, $interval, $endPeriod);
        $hours = [];

        foreach ($period as $date) {
            $hours[] = $date->format('H:i');
        }
        return collect($hours)->sliding()->map(fn($period) => $period->values())->mapWithKeys(fn($item, $key) => [$item[0] . " - " . $item[1] => $item[0] . " - " . $item[1]]);
    }

    public static function getDurations() {
        return [
            '30' => __("panel.enums.30_minutes"),
            '60' => __("panel.enums.60_minutes"),
            '90' => __("panel.enums.90_minutes"),
            '120' => __("panel.enums.120_minutes"),
        ];
    }
    static public function daysListSchema(): array {
        $schema = [];
        foreach (['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $index => $day) {
            $schema [] = Group::make([

                Checkbox::make("status")
                    ->label(__("forms.fields.weekdays.$day"))
                    ->statePath("$index.status"),

                Hidden::make("day_name")
                    ->statePath("$index.day_name")
                    ->formatStateUsing(fn()=>$day),

                TimePicker::make("from")
                    ->seconds(false)
                    ->statePath("$index.from"),
                TimePicker::make("to")
                    ->seconds(false)
                    ->statePath("$index.to"),

            ])->statePath('meta_data.days_list')
                ->columns(3);
        }
        return $schema;
    }
}

