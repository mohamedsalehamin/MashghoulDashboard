<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements HasMedia
{
    use HasTranslations, Publishable, InteractsWithMedia, SoftDeletes, HasFactory;

    protected $fillable = ['name', 'slug', 'status', 'parent_id', 'sort'];
    public $translatable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            $category->ensureSlugFromName();
        });
    }

    /**
     * When slug is empty for a locale, generate it from the name (allows manual slug editing).
     */
    public function ensureSlugFromName(): void
    {
        $slug = is_array($this->slug) ? $this->slug : (array) $this->getTranslations('slug');
        $names = $this->getTranslations('name');
        $changed = false;
        foreach ($names as $locale => $name) {
            $current = $slug[$locale] ?? '';
            if ($name && trim((string) $current) === '') {
                $slug[$locale] = Str::slug($name);
                $changed = true;
            }
        }
        if ($changed) {
            $this->slug = $slug;
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('ar')->singleFile();
        $this->addMediaCollection('en')->singleFile();
        $this->addMediaCollection('icon')->singleFile();
    }

    public function getSlugUrl(): string
    {
        return $this->getTranslation('slug', app()->getLocale()) ?: (string) $this->getKey();
    }

    public static function findBySlug(string $value): ?self
    {
        if (is_numeric($value)) {
            return static::find($value);
        }

        return static::where(function ($q) use ($value) {
            $q->where('slug->ar', $value)
              ->orWhere('slug->en', $value);
        })->first();
    }

    public function scopeParent($builder) {
        return $builder->where('parent_id', null);
    }

    public function hasParent() {
        return !is_null($this->parent_id);
    }

    public function scopeChildren($builder) {
        return $builder->where('parent_id', "!=", null);
    }

    public function children() {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function father(): BelongsTo {
        return $this->belongsTo(Category::class, 'parent_id');
    }


    public function posts() {
        return $this->hasMany(Post::class);
    }


}
