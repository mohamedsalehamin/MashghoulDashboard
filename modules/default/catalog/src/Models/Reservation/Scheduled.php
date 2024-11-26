<?php

namespace App\CatalogModule\Models\Reservation;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheduled extends Model {
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'scheduled_reservations';

    public function causer() {
        return $this->hasOne(User::class, "id",'causer_id');
    }

    public function causerLabel(): string {

        return $this->causer?->doctor ? 'doctor' : 'patient';
    }
}
