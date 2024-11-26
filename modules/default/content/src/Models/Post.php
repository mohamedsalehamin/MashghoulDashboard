<?php

namespace App\ContentModule\Models;

use App\ContentModule\Filters\ArticlesFilter;
use App\DefaultPanel\Lib\Filters\FilterScope;
use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;


class Post extends Model implements HasMedia {
    use  HasTranslations, Publishable, InteractsWithMedia , HasSlug,FilterScope;

    protected $appends = ['default'];
    protected $guarded = ['id'];
    public $translatable = ['title', 'description', 'slug'];
    protected $casts = ['publish_date' => 'datetime'];
    protected string $filterClass= ArticlesFilter::class;

    public function getDefaultAttribute() {
        return $this->getFirstMediaUrl('default');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('default');
    }
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }




}
