<?php

namespace App\ContentModule\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

/**
 * @property bool $status
 */
class Page extends Model {
    use HasFactory, HasTranslations, Publishable;
    use Sluggable;

    protected $fillable = ['title', 'description', 'slug','status'];
    public $translatable = ['title', 'description', 'slug'];


    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];

    }

}
