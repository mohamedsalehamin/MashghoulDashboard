<?php

namespace App\UsersModule\Models\Users;


use App\Models\User;

class Customer extends User {
    protected $guarded = ['id'];
    const ROLE = 'customer';


    protected static function booted() {
        parent::booted();
        static::addGlobalScope('customer', fn($query) => $query->whereHas('roles', fn($query) => $query->where('name', self::ROLE)));
        static::created(fn($patient) => $patient->assignRole(self::ROLE));
    }


}
