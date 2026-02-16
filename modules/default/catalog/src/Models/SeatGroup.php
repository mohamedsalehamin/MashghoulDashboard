<?php

namespace App\CatalogModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class SeatGroup extends Model {

    use HasTranslations;

    public array $translatable = ['title'];
    protected $guarded = ['id'];
    protected $casts = [
        'title' => 'array',
    ];

    public function seat(): BelongsTo {
        return $this->belongsTo(Seat::class);
    }
}
