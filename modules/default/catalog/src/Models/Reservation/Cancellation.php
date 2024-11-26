<?php

namespace App\CatalogModule\Models\Reservation;

use App\CatalogModule\Models\CancellationReason;
use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model {

    protected $guarded = ['id'];
    protected $table = 'reservations_cancellation';

    public function reason() {
        return $this->belongsTo(CancellationReason::class, 'reason_id');
    }

    public function getReason() {

        if ($this->cancellation_reason_id == 0) {
            return __("panel.messages.customer_not_responded");
        }
        if ($this->cancellation_reason_id == -1) {
            return __("panel.messages.manager_not_responded");
        }
        return $this->reason->name;
    }
}
