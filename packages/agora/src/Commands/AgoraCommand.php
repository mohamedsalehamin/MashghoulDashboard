<?php

namespace Packages\Agora\Commands;

use Illuminate\Console\Command;

class AgoraCommand extends Command
{
    public $signature = 'agora';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
