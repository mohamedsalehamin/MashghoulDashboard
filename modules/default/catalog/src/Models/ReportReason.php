<?php

namespace App\CatalogModule\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

class ReportReason extends Model {
    use  Publishable, HasTranslations;

    protected array $translatable = ['name'];
    protected $fillable = ['name', 'status'];
}
