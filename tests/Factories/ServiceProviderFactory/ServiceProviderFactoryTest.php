<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories\ServiceProviderFactory;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Contracts\Factories\ServiceProviderFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Services\RouteSource;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class ServiceProviderFactoryTest extends Test
{
    protected $moduleManager;
    protected $filesystem;
    protected $routeSource;

    public function setUp(): void
    {
        parent::setUp();

        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance("files", $this->filesystem);
        $this->routeSource = Mockery::mock(RouteSource::class);
        $this->instance("module.service.route_source", $this->routeSource);
    }

    /**
     * @return Mockery\MockInterface&ServiceProviderFactory
     */
    protected function getMockServiceProviderFactory (string $method) : Mockery\MockInterface
    {
        $mockMethods = $this->getClassMethods(\Thomasderooij\LaravelModules\Factories\ServiceProviderFactory::class, $method);

        $functions = implode(", ", $mockMethods);
        // Mock the module manager with all functions except the one we want to test
        $mock = Mockery::mock(\Thomasderooij\LaravelModules\Factories\ServiceProviderFactory::class."[$functions]", [$this->filesystem, $this->moduleManager, $this->routeSource]);
        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }

    protected function getMethod (string $method) : \ReflectionMethod
    {
        $reflection = new \ReflectionClass(\Thomasderooij\LaravelModules\Factories\ServiceProviderFactory::class);
        $uut = $reflection->getMethod($method);
        $uut->setAccessible(true);

        return $uut;
    }
}
