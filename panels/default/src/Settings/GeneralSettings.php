<?php

namespace App\DefaultPanel\Settings;

use App\UsersModule\Models\Provider;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Closure;
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
    public string $tax_number;
    public string $commercial_register;
    public string $reservations_fess;
    public string $app_percentage;
    public string $reservation_flow;
    public string $enabled_free_fees_in_first_reservation;
    public array $points;
    public string $app_whatsapp;
    public string $app_address;
    public float $taxes;
    public array $applications_links = [];
    public array $texts = [];
    public array $app_pages = [];
    public array $provider_pages = [];

    public array $social_links = [];
    public array $working_days = [];

    public static function group(): string {
        return 'general';
    }

    public static function getDayTimesSlot($from = '00:00', $to = '23:59',$interval=60) {
        $startPeriod = Carbon::parse($from);
        $endPeriod = Carbon::parse($to);
        $interval = "$interval minutes";
        $period = CarbonPeriod::create($startPeriod, $interval, $endPeriod);
        $hours = [];

        foreach ($period as $date) {
            $hours[] = $date->format('H:i');
        }
        return collect($hours)->sliding()->map(fn($period) => $period->values())->mapWithKeys(fn($item, $key) => [$item[0] . " - " . $item[1] => $item[0] . " - " . $item[1]]);
    }

    public static function getDurations() {
        return [
            '15' => __("panel.enums.minutes",[ 'minutes' => 15]),
            '30' => __("panel.enums.30_minutes"),
            '45' => __("panel.enums.minutes", ["minutes" => 45]),
            '60' => __("panel.enums.60_minutes"),
            '75' => __("panel.enums.minutes", ["minutes" => 75]),
            '90' => __("panel.enums.90_minutes"),
            '100' => __("panel.enums.minutes", ["minutes" => 100]),
            '120' => __("panel.enums.120_minutes"),
            130 => __("panel.enums.minutes", ["minutes" => 130]),
            140 => __("panel.enums.minutes", ["minutes" => 140]),
            150 => __("panel.enums.minutes", ["minutes" => 150]),
            160 => __("panel.enums.minutes", ["minutes" => 160]),
            180 => __("panel.enums.minutes", ["minutes" => 180]),
            200 => __("panel.enums.minutes", ["minutes" => 200]),
        ];
    }

    static public function daysListSchema(): array {
        $schema = [];

        foreach (['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $index => $day) {
            $schema [] = Group::make([

                Checkbox::make("status")
                    ->label(__("forms.fields.weekdays.$day"))
                    ->statePath("$index.status")
                    ->rules([
                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get, $index) {
                            $providerTimes = Provider::find($get("../../provider_id"));
                            if (!$get("$index.status")) {
                                return;

                            }
                            if (!$providerTimes) {

                                return;
                            }

                            $day = collect($providerTimes->meta_data['days_list'])
                                ->where('status', true)
                                ->where('day_name', $get("{$index}.day_name"))->first();


                            if (!$day) {
                                $fail(__("panel.messages.working_day_not_set"));
                                return;
                            }
                            $providerStartTime = $day['from'];
                            $providerEndTime = $day['to'];
                            $currentStartTime = $get("{$index}.from");
                            $currentEndTime = $get("{$index}.to");
                            if ($currentStartTime < $providerStartTime) {
                                $fail(__("panel.messages.start_time_less_than_provider"));
                                return;
                            }
                            if ($currentEndTime > $providerEndTime) {
                                $fail(__("panel.messages.end_time_more_than_provider"));
                                return;
                            }

                        },
                    ]),

                Hidden::make("day_name")
                    ->statePath("$index.day_name")
                    ->formatStateUsing(fn() => $day),

                TimePicker::make("from")
                    ->minutesStep(30)
                    ->datalist([
                        "00:00",
                        "00:30",
                        "01:00",
                        "01:30",
                        "02:00",
                        "02:30",
                        "03:00",
                        "03:30",
                        "04:00",
                        "04:30",
                        "05:00",
                        "05:30",
                        "06:00",
                        "06:30",
                        "07:00",
                        "07:30",
                        "08:00",
                        "08:30",
                        "09:00",
                        "09:30",
                        "10:00",
                        "10:30",
                        "11:00",
                        "11:30",
                        "12:00",
                        "12:30",
                        "13:00",
                        "13:30",
                        "14:00",
                        "14:30",
                        "15:00",
                        "15:30",
                        "16:00",
                        "16:30",
                        "17:00",
                        "17:30",
                        "18:00",
                        "18:30",
                        "19:00",
                        "19:30",
                        "20:00",
                        "20:30",
                        "21:00",
                        "21:30",
                        "22:00",
                        "22:30",
                        "23:00",
                        "23:30",


                    ])
                    ->seconds(false)
                    ->statePath("$index.from"),
                TimePicker::make("to")
                    ->minutesStep(60)
                    ->datalist([
                        "00:00",
                        "00:30",
                        "01:00",
                        "01:30",
                        "02:00",
                        "02:30",
                        "03:00",
                        "03:30",
                        "04:00",
                        "04:30",
                        "05:00",
                        "05:30",
                        "06:00",
                        "06:30",
                        "07:00",
                        "07:30",
                        "08:00",
                        "08:30",
                        "09:00",
                        "09:30",
                        "10:00",
                        "10:30",
                        "11:00",
                        "11:30",
                        "12:00",
                        "12:30",
                        "13:00",
                        "13:30",
                        "14:00",
                        "14:30",
                        "15:00",
                        "15:30",
                        "16:00",
                        "16:30",
                        "17:00",
                        "17:30",
                        "18:00",
                        "18:30",
                        "19:00",
                        "19:30",
                        "20:00",
                        "20:30",
                        "21:00",
                        "21:30",
                        "22:00",
                        "22:30",
                        "23:00",
                        "23:30",

                    ])
                    ->seconds(false)
                    ->statePath("$index.to"),

            ])->statePath('meta_data.days_list')
                ->columns(3);
        }
        return $schema;
    }

    public static function getPointsOnAction($action) {
        $points = (new self())->points;
        return match ($action) {
            'register' => $points['customer_register_action'] ?? 0,
            'dob' => $points['today_dob_customer'] ?? 0,
            'reserve' => $points['customer_reserve_action'] ?? 0,
            default => 0,
        };

    }
}

