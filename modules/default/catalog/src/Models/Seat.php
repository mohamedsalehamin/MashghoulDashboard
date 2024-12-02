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
use Spatie\Translatable\HasTranslations;
use function Clue\StreamFilter\fun;

class Seat extends Model {

    use HasFactory, Publishable, HasTranslations,SoftDeletes;

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

    public function availableTimes(Carbon $date) {
        return $this->getAvailablePeriodsOnDate($date);

    }

    public function canBookOnDate($date): bool {
        return $this->getAvailablePeriodsOnDate($date, false)->count();

    }

    public function getAvailablePeriodsOnDate($date, $skip = true) {
        $day = strtolower($date->format('l'));
        if ($skip && !$this->canBookOnDate($date)) {

            return collect([]);
        }


        $current_day = collect(array_values($this->meta_data['days_list'] ?? []))
            ->where('day_name', $day)
            ->where('status', true)
            ->first() ?? [];
        if (empty($current_day)) {
            return collect([]);
        }
        $slots = GeneralSettings::getDayTimesSlot($current_day['from'] ?? '00:00', $current_day['to'] ?? '23:59');


        if ($date->isToday()) {
            $slots = collect($slots)->filter(function ($period) {
                $from = \Str::before($period, " -");
                return Carbon::today()->setTimeFromTimeString($from)->isFuture();
            });
        }

        $reservations = $this->reservations()
            ->paid()
            ->whereDate('date', $date)
            ->pluck('from', 'to')
            ->map(function ($from, $to) {
                return Carbon::parse($from)->format("H:i") . " - " . Carbon::parse($to)->format("H:i");
            })
            ->toArray();

        return collect($slots)
            ->map(function ($slot) use ($reservations) {
                return [
                    'from' => \Str::before($slot, " -"),
                    'to' => \Str::after($slot, " - "),
                    'reserved' => in_array($slot, $reservations)
                ];
            })
//            ->sortBy(function ($time) {
//                return \Str::before($time, " -");
//            })
            ->values();

    }

    public function isAvailablePeriod($date, $period) {
        return $this->getAvailablePeriodsOnDate($date)->contains($period);

    }
}
