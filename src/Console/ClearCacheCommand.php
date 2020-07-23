<?php

namespace OZiTAG\Tager\Backend\HttpCache\Console;

use Illuminate\Console\Command;
use OZiTAG\Tager\Backend\HttpCache\HttpCache;

class ClearCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tager:http-cache-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear HTTP Cache';

    public function handle(HttpCache $cache)
    {
        $cache->clear();
    }
}
