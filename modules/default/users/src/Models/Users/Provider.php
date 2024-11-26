<?php

namespace App\UsersModule\Models\Users;


use App\ContentModule\Models\BanksAccount;
use App\ContentModule\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Provider extends User {
    protected $guarded = ['id'];
    const ROLE = 'provider';

    protected static function booted() {
        parent::booted();
        static::addGlobalScope('role', fn($query) => $query->whereHas('roles', fn($query) => $query->where('name', self::ROLE)));
        static::created(fn($patient) => $patient->assignRole(self::ROLE));
    }




}
