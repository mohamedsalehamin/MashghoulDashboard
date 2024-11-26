<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

class ContactType extends Model {
    use HasFactory, Publishable,HasTranslations;
    protected $translatable= ['name'];
    protected $fillable = ['name','status'];
}
