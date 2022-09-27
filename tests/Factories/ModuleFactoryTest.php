<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Factories\AuthServiceProviderFactory;
use Thomasderooij\LaravelModules\Factories\BroadcastServiceProviderFactory;
use Thomasderooij\LaravelModules\Factories\ConsoleKernelFactory;
use Thomasderooij\LaravelModules\Factories\ControllerFactory;
use Thomasderooij\LaravelModules\Factories\EventServiceProviderFactory;
use Thomasderooij\LaravelModules\Factories\HttpKernelFactory;
use Thomasderooij\LaravelModules\Factories\ModuleFactory;
use Thomasderooij\LaravelModules\Factories\RouteFactory;
use Thomasderooij\LaravelModules\Factories\RouteServiceProviderFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class ModuleFactoryTest extends Test
{
    public function testCreate(): void
    {
        /** @var Mockery\MockInterface&ModuleFactory $factory */
        $factory = Mockery::mock(ModuleFactory::class . "[createBaseDirectory]", [
            $filesystem = Mockery::mock(Filesystem::class),
            $routeFactory = Mockery::mock(RouteFactory::class),
            $routeServiceProviderFactory = Mockery::mock(RouteServiceProviderFactory::class),
            $consoleKernelFactory = Mockery::mock(ConsoleKernelFactory::class),
            $httpKernelFactory = Mockery::mock(HttpKernelFactory::class),
            $controllerFactory = Mockery::mock(ControllerFactory::class),
            $authServiceProviderFactory = Mockery::mock(AuthServiceProviderFactory::class),
            $broadcastServiceProviderFactory = Mockery::mock(BroadcastServiceProviderFactory::class),
            $eventServiceProviderFactory = Mockery::mock(EventServiceProviderFactory::class),
            $moduleManager = Mockery::mock(ModuleManager::class),
        ]);
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        $moduleManager->shouldReceive("isInitialised")->andReturn(true);
        $moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);
        $factory->shouldReceive("createBaseDirectory")->withArgs([$module]);
        $routeFactory->shouldReceive("create")->withArgs([$module]);
        $routeServiceProviderFactory->shouldReceive("create")->withArgs([$module]);
        $consoleKernelFactory->shouldReceive("create")->withArgs([$module]);
        $httpKernelFactory->shouldReceive("create")->withArgs([$module]);
        $controllerFactory->shouldReceive("create")->withArgs([$module]);
        $authServiceProviderFactory->shouldReceive("create")->withArgs([$module]);
        $broadcastServiceProviderFactory->shouldReceive("create")->withArgs([$module]);
        $eventServiceProviderFactory->shouldReceive("create")->withArgs([$module]);

        $factory->create($module);
    }

    public function testCreateBaseDirectory(): void
    {
        $uut = $this->getMethodFromClass("createBaseDirectory", ModuleFactory::class);
        /** @var Mockery\MockInterface&ModuleFactory $factory */
        $factory = Mockery::mock(ModuleFactory::class . "[getDirName]", [
            $filesystem = Mockery::mock(Filesystem::class),
            $routeFactory = Mockery::mock(RouteFactory::class),
            $routeServiceProviderFactory = Mockery::mock(RouteServiceProviderFactory::class),
            $consoleKernelFactory = Mockery::mock(ConsoleKernelFactory::class),
            $httpKernelFactory = Mockery::mock(HttpKernelFactory::class),
            $controllerFactory = Mockery::mock(ControllerFactory::class),
            $authServiceProviderFactory = Mockery::mock(AuthServiceProviderFactory::class),
            $broadcastServiceProviderFactory = Mockery::mock(BroadcastServiceProviderFactory::class),
            $eventServiceProviderFactory = Mockery::mock(EventServiceProviderFactory::class),
            $moduleManager = Mockery::mock(ModuleManager::class),
        ]);
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        $factory->shouldReceive("getDirName")->withArgs([$module])->andReturn($dirName = "dir/name");

        $filesystem->shouldReceive("isDirectory")->withArgs([$dirName])->andReturn(false);
        $filesystem->shouldReceive("makeDirectory")->withArgs([$dirName, 0755, true]);

        $uut->invoke($factory, $module);
    }

    public function testGetDirName(): void
    {
        $uut = $this->getMethodFromClass("getDirName", ModuleFactory::class);
        $factory = $this->app->make("module.factory.module");

        $module = "NewModule";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "root");

        $expected = base_path("$root/$module");
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }
}
