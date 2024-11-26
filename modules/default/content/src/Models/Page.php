<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

/**
 * @property bool $status
 */
class Page extends Model {
    use HasFactory, HasTranslations,Publishable;

    protected $fillable = ['title', 'description', 'status'];
    public $translatable = ['title', 'description'];


}
