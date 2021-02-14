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

    protected function getApplicationProviders($app)
    {
        $applicationProviders = parent::getApplicationProviders($app);
        $key = array_search("Illuminate\Auth\AuthServiceProvider", $applicationProviders);

        if ($key !== false) {
            $applicationProviders[$key] = AuthCompositeServiceProvider::class;
        }

        return $applicationProviders;
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

    protected function getMockableClassMethods (string $class, string $method, array $exclude = ["__construct"]) : array
    {
        $reflection = new \ReflectionClass($class);
        // Get all the methods from out module manager
        $methods = $reflection->getMethods();
        $mockFunctions = array_map(function (\ReflectionMethod $method) { return $method->getName(); }, $methods);

        // Exclude methods you don't want to mock
        foreach ($exclude as $excludeMethod) {
            $methodPosition = array_search($excludeMethod, $mockFunctions);
            unset($mockFunctions[$methodPosition]);
        }

        // And remove the function we want to test
        $testUnitPosition = array_search($method, $mockFunctions);
        if ($testUnitPosition === false) {
            throw new \Exception("That function does not exist in the $class class.");
        }
        unset($mockFunctions[$testUnitPosition]);

        return array_values($mockFunctions);
    }

    protected function getMethodFromClass (string $method, string $class) : \ReflectionMethod
    {
        $reflection = new \ReflectionClass($class);
        $uut = $reflection->getMethod($method);
        $uut->setAccessible(true);

        return $uut;
    }
}
