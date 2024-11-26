<?php

namespace App\ProviderPanel\Commands;

use Illuminate\Console\Command;

class LabPanelCommand extends Command
{
    public $signature = 'labpanel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
