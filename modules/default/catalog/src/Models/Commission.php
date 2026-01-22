<?php

namespace App\CatalogModule\Models;

use App\CatalogModule\Filters\ReservationFilter;
use App\CatalogModule\Models\Reservation\Cancellation;
use App\CatalogModule\Models\Reservation\Condition;
use App\CatalogModule\Models\Reservation\ItemsLine;
use App\CatalogModule\Models\Reservation\Prescription;
use App\CatalogModule\Models\Reservation\Rate;
use App\CatalogModule\Models\Reservation\Report;
use App\CatalogModule\Models\Reservation\Revisit;
use App\CatalogModule\Models\Reservation\Scheduled;

use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Enum\ServicesTypeEnum;
use App\DefaultPanel\Enum\TimesTypeEnum;
use App\DefaultPanel\Lib\ArrayStorage;
use App\DefaultPanel\Lib\Cart as CoreCart;
use App\DefaultPanel\Lib\Filters\FilterScope;
use App\DefaultPanel\Traits\Transactionable;
use App\DoctorPanel\Scopes\DoctorScope;
use App\Models\User;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Lab;
use App\UsersModule\Models\User\Patient;
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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Commission extends Model implements HasMedia {
    use FilterScope, InteractsWithMedia;

    protected $guarded = ['id'];
    protected $table = "reservations_commissions";

    public function reservation(): BelongsTo {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'id');
    }

    public function amount(): Attribute {

        return Attribute::make(
            get: fn($value) => Money::parse($value)
        );
    }

    public function profit() {
        return Money::parse($this->reservation->as_cart->getNetProfitTotal())->subtract($this->amount);
    }

    public function status(): Attribute {

        return Attribute::make(
            get: function ($value) {
                if (!$this->confirmed) {
                    return __('panel.enums.pending');
                }
                if ($this->confirmed && !$this->transferred) {
                    return __('panel.enums.confirmed');
                }
                return __('panel.enums.transferred');
            }
        );
    }
}
