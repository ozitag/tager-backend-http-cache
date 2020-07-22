<?php

namespace OZiTAG\Tager\Backend\HttpCache\Console;

use Illuminate\Console\Command;
use OZiTAG\Tager\Backend\HttpCache\HttpCache;
use OZiTAG\Tager\Backend\Mail\Models\TagerMailTemplate;
use OZiTAG\Tager\Backend\Mail\Repositories\MailTemplateRepository;
use OZiTAG\Tager\Backend\Banners\Repositories\BannerAreasRepository;
use OZiTAG\Tager\Backend\Seo\Models\SeoPage;
use OZiTAG\Tager\Backend\Seo\Repositories\SeoPageRepository;

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
