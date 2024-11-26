<?php

namespace App\CatalogModule\Models;


use Illuminate\Database\Eloquent\Model;

class Conversation extends Model {

    protected $guarded = ['id'];
    public function startedByContractor(): bool {
        return !is_null($this->contractor_start_at);
    }
    public function startedByCustomer(): bool {
        return !is_null($this->customer_start_at);
    }

}
