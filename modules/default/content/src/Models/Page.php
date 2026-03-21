<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\Translatable\HasTranslations;

/**
 * @property bool $status
 */
class Page extends Model implements Sitemapable
{
    use HasFactory, HasTranslations, Publishable;

    protected $fillable = ['title', 'description', 'slug', 'status', 'meta_description', 'meta_keywords'];
    public $translatable = ['title', 'description', 'slug', 'meta_description', 'meta_keywords'];

    protected static function booted(): void
    {
        static::saving(function (Page $page) {
            $page->ensureSlugFromTitle();
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
            $urls[] = Url::create("{$baseUrl}/{$locale}/{$slug}")
                ->setLastModificationDate(Carbon::parse($this->updated_at))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.6);
        }

        return $urls ?: [];
    }
}
