<?php

namespace App\ContentModule\Models;

use App\ContentModule\Filters\ArticlesFilter;
use App\DefaultPanel\Lib\Filters\FilterScope;
use App\DefaultPanel\Traits\Publishable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;


class Post extends Model implements HasMedia, Sitemapable
{
    use  HasTranslations, Publishable, InteractsWithMedia , HasSlug,FilterScope;

    protected $appends = ['default'];
    protected $guarded = ['id'];
    public $translatable = ['title', 'description', 'slug', 'meta_description', 'meta_keywords'];
    protected $casts = ['publish_date' => 'datetime'];
    protected string $filterClass= ArticlesFilter::class;

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            $post->ensureSlugFromTitle();
        });
    }

    /**
     * When slug is empty for a locale, generate it from the title (allows manual slug editing).
     */
    public function ensureSlugFromTitle(): void
    {
        $slug = is_array($this->slug) ? $this->slug : (array) $this->getTranslations('slug');
        $titles = $this->getTranslations('title');
        $changed = false;
        foreach ($titles as $locale => $title) {
            $current = $slug[$locale] ?? '';
            if ($title && trim((string) $current) === '') {
                $slug[$locale] = Str::slug($title);
                $changed = true;
            }
        }
        if ($changed) {
            $this->slug = $slug;
        }
    }

    public function getDefaultAttribute() {
        return $this->getFirstMediaUrl('default');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('default');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnCreate()
            ->doNotGenerateSlugsOnUpdate();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function toSitemapTag(): Url|string|array
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $locales = array_keys(config('laravellocalization.supportedLocales', ['ar' => [], 'en' => []]));
        $urls = [];
        foreach ($locales as $locale) {
            $slug = $this->getTranslation('slug', $locale);
            if (empty($slug)) {
                continue;
            }
            $urls[] = Url::create("{$baseUrl}/{$locale}/blog/{$slug}")
                ->setLastModificationDate(Carbon::parse($this->updated_at))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7);
        }

        return $urls ?: Url::create($baseUrl . '/ar/blog');
    }
}
