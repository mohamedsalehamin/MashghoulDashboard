<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Banner extends Model implements HasMedia
{
    use HasFactory, HasTranslations,InteractsWithMedia, Publishable;

    protected array $translatable = ['name'];

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
