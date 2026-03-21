<?php

namespace App\Console\Commands;

use App\ContentModule\Models\Page;
use App\ContentModule\Models\Post;
use App\UsersModule\Models\Provider;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap.';

    public function handle(): int
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $locales = array_keys(config('laravellocalization.supportedLocales', ['ar' => [], 'en' => []]));

        $sitemap = Sitemap::create();

        // Static routes per locale
        foreach ($locales as $locale) {
            $sitemap->add(
                Url::create("{$baseUrl}/{$locale}")
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(1.0)
            );
            $sitemap->add(
                Url::create("{$baseUrl}/{$locale}/faqs")
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
            $sitemap->add(
                Url::create("{$baseUrl}/{$locale}/contact")
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.6)
            );
            $sitemap->add(
                Url::create("{$baseUrl}/{$locale}/join")
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.6)
            );
            $sitemap->add(
                Url::create("{$baseUrl}/{$locale}/blog")
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.9)
            );
            $sitemap->add(
                Url::create("{$baseUrl}/{$locale}/reviews")
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7)
            );
            $sitemap->add(
                Url::create("{$baseUrl}/{$locale}/categories")
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.8)
            );
        }

        $sitemap->add(Post::enabled()->get());
        $sitemap->add(Page::enabled()->get());
        $sitemap->add(
            Provider::enabled()->withoutTrashed()->whereHas('user')->get()
        );

        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap written to {$path}.");

        return self::SUCCESS;
    }
}
