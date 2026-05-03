<?php

namespace Jijunair\LaravelReferral\Tests;

use Illuminate\Routing\Router;
use Jijunair\LaravelReferral\Providers\ReferralServiceProvider;
use Jijunair\LaravelReferral\Tests\Fixtures\User;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ReferralServiceProvider::class,
        ];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', str_repeat('a', 32));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('referral.redirect_route', 'home');
        $app['config']->set('referral.user_model', User::class);
    }

    protected function defineRoutes($router): void
    {
        /** @var Router $router */
        $router->get('/', fn (): string => 'home')->name('home');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
