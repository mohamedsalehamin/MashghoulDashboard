<?php

namespace App\UsersModule\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model {
    protected $table = "user_devices_token";
    public $timestamps = false;
    protected $fillable = ['token','voip_token'];
}
