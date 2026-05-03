<?php

namespace Jijunair\LaravelReferral\Providers;

use Illuminate\Support\ServiceProvider;

class ReferralServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/referral.php', 'referral');
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/referral.php' => config_path('referral.php'),
            ], 'laravel-referral-config');

            $this->publishesMigrations([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'laravel-referral-migrations');
        }

        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}
