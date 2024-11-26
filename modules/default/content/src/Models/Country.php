<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use App\CrmModule\Models\AddressBook;
use App\DefaultPanel\Traits\Publishable;

class Country extends Model {
    use HasFactory, HasTranslations, Publishable;

    public array $translatable = ['name'];


    protected $guarded = ['id'];

    public function states(): HasMany {
        return $this->hasMany(State::class);
    }

    public function addresses() {
        return $this->hasMany(AddressBook::class);
    }



}
