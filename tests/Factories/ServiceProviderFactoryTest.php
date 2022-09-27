<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Contracts\Factories\ServiceProviderFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Services\RouteSource;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class ServiceProviderFactoryTest extends Test
{
    /**
     * @var Mockery\MockInterface&ModuleManager|null
     */
    protected $moduleManager;

    /**
     * @var Mockery\MockInterface&Filesystem|null
     */
    protected $filesystem;

    /**
     * @var Mockery\MockInterface&RouteSource|null
     */
    protected $routeSource;

    /**
     * @return Mockery\MockInterface&ServiceProviderFactory
     */
    protected function getMockServiceProviderFactory(string $method, string $class = null): Mockery\MockInterface
    {
        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance("files", $this->filesystem);
        $this->routeSource = Mockery::mock(RouteSource::class);
        $this->instance("module.service.route_source", $this->routeSource);

        if ($class === null) {
            $class = \Thomasderooij\LaravelModules\Factories\ServiceProviderFactory::class;
        }
        $mockMethods = $this->getMockableClassMethods($class, $method);

        $functions = implode(", ", $mockMethods);
        // Mock the module manager with all functions except the one we want to test
        $mock = Mockery::mock($class . "[$functions]", [$this->filesystem, $this->moduleManager, $this->routeSource]);
        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }

    protected function getMethod(string $method, $class = null): \ReflectionMethod
    {
        if ($class === null) {
            $class = \Thomasderooij\LaravelModules\Factories\ServiceProviderFactory::class;
        }
        return $this->getMethodFromClass($method, $class);
    }
}
