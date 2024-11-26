<?php

namespace App\UsersModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model {
    use HasFactory;

    protected $guarded = ['id'];

    protected static function boot() {
        static::created(function ($code) {
            $code->expired_at = now()->addMinutes(5);
            $code->save();
        });
        parent::boot();
    }


    public function scopeNotExipred($builder) {
        return $builder->where('expired_at', ">=", now());
    }
}
