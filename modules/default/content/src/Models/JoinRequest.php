<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Enum\JoinRequestEnum;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class JoinRequest extends Model implements HasMedia {
    use HasFactory, InteractsWithMedia;

    protected $guarded = ['id'];
    protected $casts = [
        'password' => 'hashed',
        'status'=>JoinRequestEnum::class

    ];


}
