<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

class Slider extends Model implements HasMedia {
    use  Publishable, HasTranslations,InteractsWithMedia, HasFactory;
    protected array $translatable = ['name','image'];
    protected $fillable = [
        'status',
        'placement',
        'object_type',
        'object_id',
    ];

    public function scopePlacement(Builder $query, string $placement): Builder
    {
        return $query->where('placement', $placement);
    }
}
