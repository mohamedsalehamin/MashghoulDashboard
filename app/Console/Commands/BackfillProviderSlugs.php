<?php

namespace App\Console\Commands;

use App\UsersModule\Models\Provider;
use Illuminate\Console\Command;

class BackfillProviderSlugs extends Command
{
    protected $signature = 'providers:backfill-slugs {--force : Regenerate slugs for every provider}';

    protected $description = 'Assign unique URL slugs to providers (from name); use --force to rebuild all slugs';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $updated = 0;

        foreach (Provider::query()->orderBy('id')->cursor() as $provider) {
            if (! $force && filled($provider->slug)) {
                continue;
            }

            $provider->slug = Provider::generateUniqueSlug($provider, $provider->id);
            $provider->saveQuietly();
            $updated++;
        }

        $this->info("Updated {$updated} provider(s).");

        return self::SUCCESS;
    }
}
