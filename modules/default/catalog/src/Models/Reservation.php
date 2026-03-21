<?php

namespace App\CatalogModule\Models;

use Exception;
use Str;
use App\CatalogModule\Filters\ReservationFilter;
use App\CatalogModule\Models\Reservation\Cancellation;
use App\CatalogModule\Models\Reservation\Condition;
use App\CatalogModule\Models\Reservation\AgoraSession;
use App\CatalogModule\Models\Reservation\ItemsLine;
use App\CatalogModule\Models\Reservation\Prescription;
use App\CatalogModule\Models\Reservation\Rate;
use App\CatalogModule\Models\Reservation\Report;
use App\CatalogModule\Models\Reservation\Revisit;
use App\CatalogModule\Models\Reservation\Scheduled;

use App\CatalogModule\Models\Reservation\Timeline;
use App\DefaultPanel\Actions\RefundTransaction;
use App\DefaultPanel\Enum\LabReservationStatus;
use App\DefaultPanel\Enum\PaymentMethods;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Enum\ServicesTypeEnum;
use App\DefaultPanel\Enum\TimesTypeEnum;
use App\DefaultPanel\Lib\ArrayStorage;
use App\DefaultPanel\Lib\Cart as CoreCart;
use App\DefaultPanel\Lib\Filters\FilterScope;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Transactionable;
use App\DoctorPanel\Scopes\DoctorScope;
use App\Models\User;
use App\Notifications\AdminSendEntitlementsNotification;
use App\Notifications\ReservationCanceledFromPatientNotification;
use App\Notifications\ReservationCreatedSuccessfullyNotification;
use App\Notifications\ReservationScheduledFromDoctorNotification;
use App\Notifications\ReservationScheduledNotification;
use App\Notifications\ReservationStatusChangedNotification;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Lab;
use App\UsersModule\Models\User\Patient;
use App\UsersModule\Models\Users\Customer;
use Carbon\Carbon;
use Cknow\Money\Money;
use Darryldecode\Cart\CartCondition;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reservation extends Model {
    use Transactionable, FilterScope, AgoraSession;

    protected $filterClass = ReservationFilter::class;
    protected $guarded = ['id'];
    protected $table = 'reservations';

    protected $casts = [
        'date' => 'date',
        'from' => 'datetime',
        'meta_data' => 'array',
        'status' => ReservationStatus::class,
    ];

    public function getForeignKey() {
        return "reservation_id";
    }

    protected static function boot() {
        parent::boot();

        static::created(function (Reservation $reservation) {


        });
        static::updating(function (Reservation $reservation) {
            if ($reservation->getOriginal('status') != $reservation->status) {

                $reservation->customer->notify(new ReservationStatusChangedNotification($reservation));
                $reservation->reservable->user->notify(new ReservationStatusChangedNotification($reservation));
            }

            $reservation->addTimeline([
                'ar' => __('panel.messages.reservation_status_changed', ['status' => __('panel.enums.' . $reservation->status->value, [], 'ar')], 'ar'),
                'en' => __('panel.messages.reservation_status_changed', ['status' => __('panel.enums.' . $reservation->status->value, [], 'en')], 'en')
            ], $reservation->status);
        });

    }

    public function getDurationAttribute() {
        return __("panel.enums.minutes", ['minutes' => $this->as_cart->getContent()->sum(fn($item) => $item->associatedModel?->duration)]);
    }

    public function scopeBelongsToAuthUser($builder) {
        return $builder->where('reservable_type', Lab::class)->where('reservable_id', provider()?->id);
    }

    public function scopeStartSoon($builder) {
        return $builder->where('date', ">=", now())->where('date', "<=", now()->addMinutes(5));
    }

    public function scopeTimeIsUp($builder) {
        return $builder->where('status', ReservationStatus::PROCESSING)->where('date', "<=", now()->addMinutes(30));
    }

    public function scopeToday($builder) {

        return $builder->whereBetween('date', [now()->startOfDay(), now()->endOfDay()])->orderBy('from', 'asc');
    }

    public function customer(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commission(): HasOne {
        return $this->hasOne(Commission::class);
    }


    public function price(): Attribute {

        return Attribute::make(
            get: fn($value) => Money::parse($value)
        );
    }

    public function dateTime(): Attribute {

        return Attribute::make(
            get: fn() => $this->date->setTimeFromTimeString(explode(' - ', $this->period)[0])
        );
    }

    public function canRevisit() {
        return !$this->revisit()->exists() && $this->isDoctorReservation() && $this->completed() && $this->prescription()->exists() && $this->prescription->has_visit_reservation;
    }

    public function canConfirm(): bool {
        return $this->status == LabReservationStatus::PENDING;
    }

    public function isRunning() {
        try {

            $startDate = $this->date->setTimeFromTimeString(explode(' - ', $this->period)[0]);
            $endDate = $this->date->setTimeFromTimeString(explode(' - ', $this->period)[1]);
            return $startDate <= now() && $endDate >= now();
        } catch (Exception $e) {
            return false;
        }
    }


    public function completed() {
        return $this->status == ReservationStatus::COMPLETED;
    }

    public function scopeCompleted($builder) {
        return $builder->where('status', ReservationStatus::COMPLETED);
    }

    public function getReservationNumberAttribute(): string {
        return sprintf("6%'.09d", $this->id);
    }


    function itemsLine(): HasMany {
        return $this->hasMany(ItemsLine::class);
    }

    function conditions(): HasMany {
        return $this->hasMany(Condition::class);
    }

    public function getAsCartAttribute() {
        $eventsClass = config('shopping_cart.events');
        $events = $eventsClass ? new $eventsClass() : app('events');
        $session_key = md5($this->cart_id . Str::random());
        $instanceName = $session_key . 'back_end_order_cart';
        $cart = new CoreCart(
            new ArrayStorage,
            $events,
            $instanceName,
            $session_key,
            config('shopping_cart')
        );

        $this->itemsLine->transform(function (ItemsLine $item) {

            $conditions = collect($item->conditions)->map(/**
             * @throws InvalidConditionException
             */ fn($cond) => new CartCondition($cond))->toArray();
            $item['quantity'] = $item->quantity > 0 ? $item->quantity : 1;
            $item['associatedModel'] = Service::find($item->model['id']);
            $item['new_conditions'] = $conditions;
            return $item;
        })->each(function ($item) use ($cart) {
            $d = $item->toArray();
            $d['conditions'] = $d['new_conditions'];
            return $cart->add($d);
        });
        $this->conditions->each(/**
         * @throws InvalidConditionException
         */ fn($condition) => $cart->condition(new CartCondition($condition->toArray())));

        return $cart;
    }

    public function getPrintCartAttribute()
    {
        $totalProducts = 0;
        $eventsClass = config('shopping_cart.events');
        $events = $eventsClass ? new $eventsClass() : app('events');
        $session_key = md5($this->cart_id . Str::random());
        $instanceName = $session_key . 'back_end_order_cart';
        $cart = new CoreCart(
            new ArrayStorage,
            $events,
            $instanceName,
            $session_key,
            config('shopping_cart')
        );

        $this->itemsLine->transform(function (ItemsLine $item) use (&$totalProducts) {
            foreach ($item['attributes']['products'] as $product) {
                $price = $product['sale_price']['amount'] > 0 ? $product['sale_price']['amount'] : $product['price']['amount'];
                $totalProducts += ($price * ($product['quantity'] ?? 1)) / 100;
            }
            $conditions = collect($item->conditions)->map(
                /**
                 * @throws InvalidConditionException
                 */
                fn($cond) => new CartCondition($cond)
            )->toArray();

            $item['quantity'] = $item->quantity > 0 ? $item->quantity : 1;
            $item['associatedModel'] = Service::find($item->model['id']);
            $item['price'] = Service::find($item->model['id'])->price->formatByDecimal();
            $item['sale_price'] = Service::find($item->model['id'])->sale_price->formatByDecimal();
            $item['new_conditions'] = $conditions;
            return $item;
        })->each(function ($item) use ($cart) {
            $d = $item->toArray();
            $d['conditions'] = $d['new_conditions'];
            // Use sale price if available, otherwise use regular price
            $price = $d['sale_price'] > 0 ? $d['sale_price'] : $d['price'];
            $d['price'] = $price;
            return $cart->add($d);
        });

        $this->conditions
            ->filter(fn($condition) => $condition->type !== 'products')
            ->each(
                /**
                 * @throws InvalidConditionException
                 */
                fn($condition) => $cart->condition(new CartCondition($condition->toArray()))
            );
        $cart->condition(new CartCondition([
            "name" => "products",
            "type" => "products",
            "target" => "subtotal",
            "value" => floatval($totalProducts),
            "order" => 1,
        ]));
        return $cart;
    }
    public function rate() {
        return $this->hasOne(Rate::class);
    }

    public function rated() {
        return $this->rate()->exists();
    }


    public function canRate(): bool {
        return !$this->rate()->exists() && $this->status == ReservationStatus::COMPLETED;
    }


    public function reservable(): MorphTo {
        return $this->morphTo();
    }

    public function report(): HasOne {
        return $this->hasOne(Report::class);
    }


    public function getAvailableStatus() {

        $statuses = collect(ReservationStatus::cases())->map(fn($status) => $status->value);
        $standardStatus = collect($statuses)
            ->splice(1, $statuses->count())
            ->map(function ($status) {
                $statusLabel = $status;

                return [
                    'value' => $status,
                    'label' => __("panel.enums." . $statusLabel)
                ];
            })
            ->values();
        $standardStatus = $standardStatus->values();
        if (auth()->user()->hasRole('provider')) {
            $standardStatus = $standardStatus->filter(fn($status) => $status['value'] != ReservationStatus::CANCELED->value);
        }
        return $standardStatus;
    }

    public function serviceRate() {
        return $this->rate()->where('type', 'service');
    }

    public function placeRate() {
        return $this->rate()->where('type', 'place');
    }

    public function rates() {
        return $this->hasMany(Rate::class);
    }

    public function seat() {
        return $this->belongsTo(Seat::class);
    }

    public function addTimeline($title, $status): void {
        $this->timeline()->create([
            'title' => $title,
            'status' => $status,
        ]);
    }

    public function timeline() {
        return $this->hasMany(Timeline::class);
    }

    // public function getPaymentStatus(): ReservationPaymentStatus {

    //     return $this->transactions()->count() == $this->transactions()->where('status', 'paid')->count() ? ReservationPaymentStatus::PAID : ReservationPaymentStatus::PENDING;
    // }
    public function getPaymentStatus()
    {
        if ($this->transactions()->count() == 0) {
            return ReservationPaymentStatus::PENDING;
        }
        
        $currentStatus = $this->transactions()->latest()->first()->status;
        return $currentStatus ?: ReservationPaymentStatus::PENDING;
    }
}
