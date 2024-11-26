<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

class Faq extends Model
{
    use HasFactory,Publishable,HasTranslations;
    protected $translatable= ['question','answer'];
    protected $fillable = ['question','answer','status','location'];
}
