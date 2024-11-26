<?php

namespace App\CatalogModule\Models\Reservation;

use App\CatalogModule\Models\CancellationReason;
use App\CatalogModule\Models\ReportReason;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model {

    protected $guarded = ['id'];

    protected $table = 'reservations_report';

    public function reason(): BelongsTo {
        return $this->belongsTo(ReportReason::class, 'reason_id');
    }


}
