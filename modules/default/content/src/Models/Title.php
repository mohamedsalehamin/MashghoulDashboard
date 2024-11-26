<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Title extends Model {
    use HasFactory, HasTranslations;

    public array $translatable = ['name'];
}
