<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderOption extends Model {
    protected $guarded = ['id'];
    protected $casts = [
        'texts' => 'array',
    ];
    use HasFactory;
}
