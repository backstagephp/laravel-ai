<?php

namespace Backstage\Laravel\AI\Commands;

use Illuminate\Console\Command;

class AICommand extends Command
{
    public $signature = 'ai';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
