<?php

namespace App\CatalogModule\Models;

use Str;
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
    protected $guarded = ['id', 'serviceGroups'];
    protected $casts = [
        'meta_data' => 'array'
    ];

    public function serviceGroups(): HasMany {
        return $this->hasMany(SeatGroup::class);
    }

    public function services(): BelongsToMany {
        return $this->belongsToMany(Service::class, 'seat_service')
            ->withPivot('service_group_id');
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
        $slots = [];
        if ($skip && !$this->canBookOnDate($date, $interval)) {

            return collect([]);
        }

        $list = collect(GeneralSettings::flatActiveSeatDaysList($this->meta_data['days_list'] ?? []))
            ->where('day_name', $day)
            ->values();

        if (empty($list)) {
            return collect([]);
        }

        foreach ($list as $period) {

            $slots[] = GeneralSettings::getDayTimesSlot($period['from'] ?? '00:00', $period['to'] ?? '23:59', $interval);
        }


        if ($date->isToday()) {

            $slots = collect($slots)->map(function ($slot) {

              return   $slot->filter(function ($period) {

                    $from = Str::before($period, " -");

                    return Carbon::today()->setTimeFromTimeString($from)->isFuture();
                });
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
            ->map(function ($slot) use ($reservations) {
                return collect($slot)
                    ->map(function ($slot) {
                        return [
                            'from' => Str::before($slot, " -"),
                            'to' => Str::after($slot, " - "),
                            'reserved' => false
                        ];
                    })->map(function ($slot) use ($reservations) {
                        $slot['reserved'] = (bool)collect($reservations)
                            ->where('from', "<=", $slot['to'])
                            ->where('to', ">", $slot['from'])
                            ->count();
                        return $slot;

                    })->values();
            })->values();

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
