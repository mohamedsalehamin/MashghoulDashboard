<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Enum\TimesTypeEnum;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Publishable;
use App\UsersModule\Models\Provider;
use Carbon\Carbon;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use function Clue\StreamFilter\fun;

class Seat extends Model {

    use HasFactory, Publishable, HasTranslations, SoftDeletes;
    use LogsActivity;

    public array $translatable = ['title'];
    protected $guarded = ['id'];
    protected $casts = [
        'meta_data' => 'array'
    ];

    public function services(): BelongsToMany {
        return $this->belongsToMany(Service::class);
    }

    public function provider(): BelongsTo {
        return $this->belongsTo(Provider::class);
    }

    public function reservations(): HasMany {
        return $this->hasMany(Reservation::class);
    }

    public function availableTimes(Carbon $date, $interval = 60) {

        return $this->getAvailablePeriodsOnDate($date, true, $interval);

    }

    public function canBookOnDate($date, $interval = 60): bool {
        return $this->getAvailablePeriodsOnDate($date, false, $interval)->count();

    }

    public function getAvailablePeriodsOnDate($date, $skip = true, $interval = 60) {
        $day = strtolower($date->format('l'));
        if ($skip && !$this->canBookOnDate($date, $interval)) {

            return collect([]);
        }

        $current_day = collect(array_values($this->meta_data['days_list'] ?? []))
            ->where('day_name', $day)
            ->where('status', true)
            ->first() ?? [];
        if (empty($current_day)) {
            return collect([]);
        }


        $slots = GeneralSettings::getDayTimesSlot($current_day['from'] ?? '00:00', $current_day['to'] ?? '23:59', $interval);

        if ($date->isToday()) {
            $slots = collect($slots)->filter(function ($period) {
                $from = \Str::before($period, " -");
                return Carbon::today()->setTimeFromTimeString($from)->isFuture();
            });
        }

        $reservations = $this->reservations()
//            ->paid()
            ->whereDate('date', $date->format("Y-m-d"))
            ->pluck('from', 'to')
            ->map(fn($from, $to) => ['from' => Carbon::parse($from)->format("H:i"), 'to' => Carbon::parse($to)->format('H:i')])
            ->values()
            ->toArray();

        return collect($slots)
            ->map(function ($slot) {

                return [
                    'from' => \Str::before($slot, " -"),
                    'to' => \Str::after($slot, " - "),
                    'reserved' => false
                ];
            })->map(function ($slot) use ($reservations) {

                $slot['reserved'] = collect($reservations)
                    ->where('from', ">=", $slot['from'])
                    ->where('to', "<=", $slot['to'])
                    ->count();
                return $slot;
            })

//            ->sortBy(function ($time) {
//                return \Str::before($time, " -");
//            })
            ->values();

    }

    public function isAvailablePeriod($date, $period) {
        return $this->getAvailablePeriodsOnDate($date)->contains($period);

    }

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['price'])
            ->logOnly(['provider.name', 'title', 'status',]);
        // Chain fluent methods for configuration options

    }
}
