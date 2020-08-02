<?php

namespace OZiTAG\Tager\Backend\HttpCache;

use Illuminate\Support\ServiceProvider;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use OZiTAG\Tager\Backend\HttpCache\Console\ClearCacheCommand;

class HttpCacheServiceProvider extends ServiceProvider
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
            $instance = new HttpCache($this->app->make('files'));

            return $instance->setContainer($this->app);
        });

        $this->publishes([
            __DIR__ . '/../config.php' => config_path('tager-http-cache.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearCacheCommand::class
            ]);
        }
    }
}
