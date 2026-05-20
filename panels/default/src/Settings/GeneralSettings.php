<?php

namespace App\DefaultPanel\Settings;

use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use App\UsersModule\Models\Provider;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TimePicker;
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings {
    public string|null $app_logo;
    public string $app_name;
    public string $app_email;
    public string $app_phone;
    public string $tax_number;
    public string $commercial_register;
    public float $reservations_fess;
    public string $reservation_flow;
    public string $enabled_free_fees_in_first_reservation;
    public string $enabled_whatsapp_icon;
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

    public ?string $code_before_end_head_tag = null;
    public ?string $code_after_body_tag = null;
    public ?string $code_before_end_body_tag = null;

    public static function group(): string {
        return 'general';
    }

    public static function getDayTimesSlot($from = '00:00', $to = '23:59', $interval = 60) {
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
        $all_durations = [
            // '15' => __("panel.enums.minutes", ['minutes' => 15]),
            // '30' => __("panel.enums.30_minutes"),
            // '45' => __("panel.enums.minutes", ["minutes" => 45]),
            // '60' => __("panel.enums.60_minutes"),
            // '75' => __("panel.enums.minutes", ["minutes" => 75]),
            // '90' => __("panel.enums.90_minutes"),
            // '100' => __("panel.enums.minutes", ["minutes" => 100]),
            // '120' => __("panel.enums.120_minutes"),
            // 130 => __("panel.enums.minutes", ["minutes" => 130]),
            // 140 => __("panel.enums.minutes", ["minutes" => 140]),
            // 150 => __("panel.enums.minutes", ["minutes" => 150]),
            // 160 => __("panel.enums.minutes", ["minutes" => 160]),
            // 180 => __("panel.enums.minutes", ["minutes" => 180]),
            // 200 => __("panel.enums.minutes", ["minutes" => 200]),
            // 200 => __("panel.enums.minutes", ["minutes" => 200]),

            '15' => __("panel.enums.minutes", ['minutes' => 15]),
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
            220 => __("panel.enums.minutes", ["minutes" => 220]),
            240 => __("panel.enums.minutes", ["minutes" => 240]),
            260 => __("panel.enums.minutes", ["minutes" => 260]),
            280 => __("panel.enums.minutes", ["minutes" => 280]),
            300 => __("panel.enums.minutes", ["minutes" => 300]),
            320 => __("panel.enums.minutes", ["minutes" => 320]),
            340 => __("panel.enums.minutes", ["minutes" => 340]),
            360 => __("panel.enums.minutes", ["minutes" => 360]),
            380 => __("panel.enums.minutes", ["minutes" => 380]),
            400 => __("panel.enums.minutes", ["minutes" => 400]),
        ];

        for ($i = 400; $i <= 1440; $i += 20) {
            $all_durations[(string)$i] = __("panel.enums.minutes", ['minutes' => $i]);
        }

        return $all_durations;
    }

    /**
     * Weekdays the provider marked active in profile (edit profile → working times).
     *
     * @return list<string> e.g. ['sunday','monday']
     */
    public static function activeProviderDayNames(?Provider $provider = null): array
    {
        $provider = $provider ?? (function_exists('provider') ? \provider() : null);
        if (! $provider) {
            return [];
        }

        $weekOrder = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $active = collect($provider->meta_data['days_list'] ?? [])
            ->filter(fn ($row) => is_array($row) && ! empty($row['status']))
            ->pluck('day_name')
            ->filter()
            ->unique()
            ->values();

        return array_values(array_intersect($weekOrder, $active->all()));
    }

    /**
     * Build seat default days_list: only profile-active days, times copied from profile.
     *
     * @return list<array{status: bool, day_name: string, from: string, to: string}>
     */
    public static function defaultSeatDaysListFromProvider(?Provider $provider = null): array
    {
        $provider = $provider ?? (function_exists('provider') ? \provider() : null);
        $names = self::activeProviderDayNames($provider);
        if ($names === [] || ! $provider) {
            return [];
        }

        $daysList = collect($provider->meta_data['days_list'] ?? []);
        $out = [];
        foreach ($names as $index => $dayName) {
            $row = $daysList->firstWhere('day_name', $dayName);
            $out[] = [
                'status' => true,
                'day_name' => $dayName,
                'from' => $row['from'] ?? '09:00',
                'to' => $row['to'] ?? '22:00',
            ];
        }

        return $out;
    }

    /**
     * Keep only days still active on the provider profile; reindex 0..n for the form schema.
     *
     * @param  list<array<string, mixed>>  $rawDaysList
     * @return list<array<string, mixed>>
     */
    public static function filterSeatDaysListForProvider(array $rawDaysList, ?Provider $provider = null): array
    {
        $provider = $provider ?? (function_exists('provider') ? \provider() : null);
        $active = self::activeProviderDayNames($provider);
        if ($active === [] || ! $provider) {
            return [];
        }

        $byName = collect(array_values($rawDaysList))->keyBy('day_name');
        $provDays = collect($provider->meta_data['days_list'] ?? []);
        $out = [];
        foreach ($active as $dayName) {
            $existing = $byName->get($dayName);
            $prov = $provDays->firstWhere('day_name', $dayName);
            $out[] = [
                'status' => (bool) ($existing['status'] ?? true),
                'day_name' => $dayName,
                'from' => $existing['from'] ?? $prov['from'] ?? '09:00',
                'to' => $existing['to'] ?? $prov['to'] ?? '22:00',
            ];
        }

        return $out;
    }

    /**
     * Normalize seat days_list for display/API: supports flat day rows or nested shift arrays.
     *
     * @param  list<mixed>  $daysList
     * @return list<list<array<string, mixed>>>
     */
    public static function normalizeSeatDaysListToShifts(array $daysList): array
    {
        $daysList = array_values($daysList);
        if ($daysList === []) {
            return [];
        }

        $first = $daysList[0];
        if (! is_array($first)) {
            return [];
        }

        if (array_key_exists('day_name', $first)) {
            $active = array_values(array_filter(
                $daysList,
                fn ($day) => is_array($day) && ! empty($day['status'])
            ));

            return $active === [] ? [] : [$active];
        }

        $shifts = [];
        foreach ($daysList as $shift) {
            if (! is_array($shift)) {
                continue;
            }
            if (array_key_exists('day_name', $shift)) {
                $shift = [$shift];
            }
            $active = array_values(array_filter(
                $shift,
                fn ($day) => is_array($day) && ! empty($day['status'])
            ));
            if ($active !== []) {
                $shifts[] = $active;
            }
        }

        return $shifts;
    }

    /**
     * @param  list<string>|null  $onlyDayNames  If null, all weekdays. If empty array, returns no schema rows.
     */
    static public function daysListSchema(?array $onlyDayNames = null): array {
        $weekOrder = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        if ($onlyDayNames === null) {
            $days = $weekOrder;
        } else {
            $days = array_values(array_intersect($weekOrder, $onlyDayNames));
        }

        $schema = [];

        foreach ($days as $index => $day) {
            $schema [] = Group::make([

                Checkbox::make("status")
                    ->label(__("forms.fields.weekdays.$day"))
                    ->statePath("$index.status")
                    ->rules([
                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get, $index) {
                            $providerTimes = Provider::find($get("../../../provider_id"));

                            if (!$get("$index.status")) {
                                return;

                            }
                            if (!$providerTimes) {

                                return;
                            }

                            $day = collect($providerTimes->meta_data['days_list'] ?? [])
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
                    // ->minutesStep(60)
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

            ])
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

