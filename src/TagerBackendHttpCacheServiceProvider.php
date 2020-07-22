<?php

namespace OZiTAG\Tager\Backend\HttpCache;

use Illuminate\Support\ServiceProvider;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use OZiTAG\Tager\Backend\Mail\Commands\FlushMailTemplatesCommand;
use OZiTAG\Tager\Backend\Banners\Commands\FlushBannersCommand;
use OZiTAG\Tager\Backend\Settings\Commands\FlushSettingsCommand;

class TagerBackendHttpCacheServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(HttpCache::class, function () {
            $instance = new Cache($this->app->make('files'));

            return $instance->setContainer($this->app);
        });

        $this->publishes([
            __DIR__ . '/../config.php' => config_path('tager-http-cache.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                //
            ]);
        }
    }
}
