<?php

namespace App\ContentModule\Models;

use App\UsersModule\Models\Clinic;
use App\UsersModule\Models\User\Doctor;
use App\UsersModule\Models\Lab;
use App\UsersModule\Models\User\Patient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use App\CrmModule\Models\AddressBook;
use App\DefaultPanel\Traits\Publishable;

class City extends Model {
    use HasFactory, HasTranslations, Publishable;

    public array $translatable = ['name'];


    protected $guarded = ['id'];

    public function state(): BelongsTo {
        return $this->belongsTo(State::class);
    }

}
