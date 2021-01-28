<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests;

use Orchestra\Testbench\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Thomasderooij\LaravelModules\CompositeProviders\AuthCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\EventCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\RouteCompositeServiceProvider;
use Thomasderooij\LaravelModules\Console\CompositeKernel as ConsoleKernel;
use Thomasderooij\LaravelModules\ConsoleSupportServiceProvider;
use Thomasderooij\LaravelModules\Http\CompositeKernel as HttpKernel;
use Thomasderooij\LaravelModules\ModuleServiceProvider;

abstract class Test extends TestCase
{
    use MatchesSnapshots;

    protected function setUp (): void
    {
        parent::setUp();
    }

    protected function tearDown () : void
    {
        parent::tearDown();
    }

    protected function getPackageProviders ($app) : array
    {
        return [
            AuthCompositeServiceProvider::class,
            EventCompositeServiceProvider::class,
            RouteCompositeServiceProvider::class,
            ModuleServiceProvider::class,
            ConsoleSupportServiceProvider::class,
        ];
    }

    /**
     * Set up the environment
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp ($app) : void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'mysql',
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ]);
    }

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', ConsoleKernel::class);
    }

    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', HttpKernel::class);
    }
}
