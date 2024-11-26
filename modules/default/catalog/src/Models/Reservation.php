<?php

namespace App\CatalogModule\Models;

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
        'service_type' => ServicesTypeEnum::class,
        'reserve_type' => TimesTypeEnum::class,
        'status' => ReservationStatus::class,
    ];

    public function getForeignKey() {
        return "reservation_id";
    }

    protected static function boot() {
        parent::boot();

        static::created(function (Reservation $reservation) {
            $settings = new GeneralSettings();
            $percentage = $reservation->reservable->getMorphClass() == Doctor::class ? $settings->doctors_app_percentage : $settings->labs_app_percentage;

            $amount = ($reservation->price->formatByDecimal() / 100) * $percentage;
            $reservation->commission()->create([
                'percentage' => $percentage,
                'amount' => $amount
            ]);


        });
        static::updating(function (Reservation $reservation) {
            if ($reservation->getOriginal('status') != $reservation->status && !in_array($reservation->status, [ReservationStatus::COMPLETED, ReservationStatus::PATIENT_CANCELED, ReservationStatus::PATIENT_RESCHEDULED])) {

                $reservation->patient->notify(new ReservationStatusChangedNotification($reservation));
                $reservation->reservable->user->notify(new ReservationStatusChangedNotification($reservation));
            }
            if ($reservation->status == ReservationStatus::PATIENT_CANCELED) {
                $reservation->patient->notify(new ReservationCanceledFromPatientNotification($reservation));
                $reservation->reservable->user->notify(new ReservationCanceledFromPatientNotification($reservation));
            }
            if ($reservation->status == ReservationStatus::PATIENT_RESCHEDULED) {
                $reservation->patient->notify(new ReservationScheduledNotification($reservation));
                $reservation->reservable->user->notify(new ReservationScheduledNotification($reservation));
            }
            $reservation->addTimeline([
                'ar' => __('panel.messages.reservation_status_changed', ['status' => __('panel.enums.' . $reservation->status->value, [], 'ar')], 'ar'),
                'en' => __('panel.messages.reservation_status_changed', ['status' => __('panel.enums.' . $reservation->status->value, [], 'ar')], 'en')
            ], $reservation->status);
        });

    }

    public function scopeBelongsToAuthUser($builder) {
        return  $builder->where('reservable_type', Lab::class)->where('reservable_id', provider()?->id);
    }

    public function scopeStartSoon($builder) {
        return $builder->where('date', ">=", now())->where('date', "<=", now()->addMinutes(5));
    }

    public function scopeTimeIsUp($builder) {
        return $builder->where('status', ReservationStatus::PROCESSING)->where('date', "<=", now()->addMinutes(30));
    }

    public function scopeToday($builder) {

        return $builder->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
    }

    public function scopeConsultations($builder) {
        return $builder->where('reservable_type', Doctor::class);
    }

    public function scopeTests($builder) {
        return $builder->where('reservable_type', Lab::class);
    }

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class, 'user_id');
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
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isDoctorReservation(): bool {
        return $this->reservable_type == \App\UsersModule\Models\Doctor::class;
    }

    public function isLabReservation(): bool {
        return $this->reservable_type == Lab::class;
    }

    public function completed() {
        return $this->status == ReservationStatus::COMPLETED;
    }

    public function getReservationNumberAttribute(): string {
        return sprintf("%'.06d", $this->id);
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
        $session_key = md5($this->cart_id . \Str::random());
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
            $item['associatedModel'] = (object)$item->model;
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


    public function rate() {
        return $this->hasOne(Rate::class);
    }

    public function rated() {
        return $this->rate()->exists();
    }


    public function cancellation(): HasOne {
        return $this->hasOne(Cancellation::class);
    }

    public function cancel($reason) {
        $fees = 10;
        $reservationDateAgo = Carbon::parse($this->date_time)->diffInHours();
        $reservationPrice = $this->price->formatByDecimal();

        if ($reservationDateAgo < 24 && $reservationDateAgo > 3) {
            $fees = 30;
        }
        if ($reservationDateAgo < 3) {
            $fees = 50;
        }
        $fees = $reservationPrice * $fees / 100;

        $amount = $reservationPrice - $fees;

        $response = RefundTransaction::run($this->transaction->meta_data['invoiceId'], $amount);

        $status = auth()->id() == $this->user_id ? ReservationStatus::PATIENT_CANCELED : ReservationStatus::DOCTOR_CANCELED;
        $this->update(['status' => $status]);


        $this->cancellation()->create(['reason_id' => $reason]);
        $this->transaction->update(['status' => ReservationPaymentStatus::REFUNDED,'meta_data' => array_merge($this->transaction->meta_data,['refund_data' =>$response->Data])]);

    }


    public function canRate(): bool {
        return !$this->rate()->exists() && $this->status == ReservationStatus::COMPLETED;
    }

    public function prescription(): HasOne {
        return $this->hasOne(Prescription::class);
    }

    public function reservable(): MorphTo {
        return $this->morphTo();
    }

    public function report(): HasOne {
        return $this->hasOne(Report::class);
    }

    public function isOnline(): bool {
        return true;
        return in_array($this->service_type, [ServicesTypeEnum::VOICE, ServicesTypeEnum::VIDEO]);
    }

    public function schedule(): HasOne {
        return $this->hasOne(Scheduled::class);
    }

    public function revisit(): HasOne {
        return $this->hasOne(Revisit::class);
    }

    public function sharedAnalysis(): BelongsToMany {
        return $this->belongsToMany(ItemsLine::class, 'shared_analysis_reservation', 'reservation_id', 'analysis_id');
    }

    public function scheduleAppointment($date, $period, $causer = 'doctor'): void {
        $status = $causer == 'doctor' ? ReservationStatus::DOCTOR_RESCHEDULED : ReservationStatus::PATIENT_RESCHEDULED;

        $this->update(['status' => $status]);
        $this->schedule()->create([
            'date' => $date,
            'period' => $period,
            'status' => 'pending',
            'causer_id' => $causer == 'doctor' ? $this->reservable?->user?->id : $this->user_id
        ]);

        $this->replicate()->fill(['date' => $date, 'period' => $period, 'parent_id' => $this->id])->save();
        if ($causer == 'doctor') {
            $this->patient->notify(new ReservationScheduledFromDoctorNotification($this));
            $this->reservable->user->notify(new ReservationScheduledFromDoctorNotification($this));
            return;;
        }
        $this->patient->notify(new ReservationScheduledNotification($this));
        $this->reservable->user->notify(new ReservationScheduledNotification($this));
    }

    public function revisitAppointment($date, $period): void {

        $this->revisit()->create([
            'date' => $date,
            'period' => $period,
        ]);
        $this->replicate()->fill(['date' => $date, 'period' => $period, 'parent_id' => $this->id])->save();
    }

    public function timeline() {
        return $this->hasMany(Timeline::class);
    }

    public function canCancel() {
        return $this->report()->doesntExist() && $this->cancellation()->doesntExist() && !$this->completed();
    }

    public function canReport() {
        return $this->report()->doesntExist() && $this->cancellation()->doesntExist() && !$this->completed();
    }

    public function canReschedule(): bool {
        if ($this->schedule()->exists()) {
            return false;
        }
        if ($this->completed()) {
            return false;
        }
        if ($this->isLabReservation()) {
            return Carbon::parse($this->dateTime)->isFuture();
        }

        return now()->diffInHours(Carbon::parse($this->dateTime), false) > -24;
    }

    public function addTimeline($title, $status): void {
        $this->timeline()->create([
            'title' => $title,
            'status' => $status,
        ]);
    }
}
