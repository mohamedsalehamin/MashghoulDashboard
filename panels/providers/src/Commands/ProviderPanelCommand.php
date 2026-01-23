<?php

namespace App\ProviderPanel\Commands;

use Illuminate\Console\Command;

class ProviderPanelCommand extends Command
{
    public $signature = 'providerpanel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
