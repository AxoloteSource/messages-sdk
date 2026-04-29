<?php

namespace AxoloteSource\MessagesSdk;

use Illuminate\Support\ServiceProvider;

class AxMessagesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/axMessages.php' => config_path('axMessages.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/axMessages.php', 'axMessages');
    }
}
