<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Console\Kernel;
use Thomasderooij\LaravelModules\Factories\ConsoleKernelFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Services\RouteSource;
use Thomasderooij\LaravelModules\Tests\Test;

class ConsoleKernelFactoryTest extends Test
{
    public function testCreate(): void
    {
        /** @var Mockery\MockInterface&ConsoleKernelFactory $factory */
        $factory = Mockery::mock(
            ConsoleKernelFactory::class . "[" . implode(
                ", ",
                $this->getMockableClassMethods(ConsoleKernelFactory::class, "create")
            ) . "]",
            [
                $this->app->make('files'),
                $this->app->make("module.service.manager"),
                $this->app->make("module.service.route_source")
            ]
        );
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        $factory->shouldReceive("getConsoleDir")->withArgs([$module])->andReturn($consoleDir = "ConsoleDir");
        $factory->shouldReceive("getKernelFileName")->andReturn($kernelFileName = "KernelFile");
        $factory->shouldReceive("getStub")->andReturn($stub = "stub");
        $factory->shouldReceive("getKernelNamespacePlaceholder")->andReturn($kernelNsPh = "kernelNsPh");
        $factory->shouldReceive("getKernelNamespace")->andReturn($kernelNs = "kernelNs");
        $factory->shouldReceive("getModuleKernelPlaceholder")->andReturn($mkPh = "mkPh");
        $factory->shouldReceive("getModuleKernel")->andReturn($mk = "mk");
        $factory->shouldReceive("getKernelConsoleDirPlaceholder")->andReturn($consoleDirPh = "consoleDirPh");
        $factory->shouldReceive("getKernelConsoleDir")->andReturn($cd = "cd");
        $factory->shouldReceive("getRouteFilePlaceholder")->andReturn($rfPh = "rfPh");
        $factory->shouldReceive("getRelativeRouteFilePath")->andReturn($path = "path");
        $factory->shouldReceive("populateFile")->withArgs([
            $consoleDir,
            $kernelFileName,
            $stub,
            [
                $kernelNsPh => $kernelNs,
                $mkPh => $mk,
                $consoleDirPh => $cd,
                $rfPh => $path
            ]
        ]);

        $factory->create($module);
    }

    public function testGetConsoleDir(): void
    {
        $uut = $this->getMethodFromClass("getConsoleDir", ConsoleKernelFactory::class);
        $factory = Mockery::mock(ConsoleKernelFactory::class . "[getConsoleDirectory]", [
            $this->app->make('files'),
            $this->app->make("module.service.manager"),
            $this->app->make("module.service.route_source")
        ]);
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "root");
        $factory->shouldReceive("getConsoleDirectory")->andReturn($dir = "console_directory");

        $expected = base_path("$root/$module/$dir");
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }

    public function testGetRelativeRouteFilePath(): void
    {
        $routeSource = Mockery::mock(RouteSource::class);

        $uut = $this->getMethodFromClass("getRelativeRouteFilePath", ConsoleKernelFactory::class);
        $factory = Mockery::mock(
            ConsoleKernelFactory::class . "[getRelativeRouteRootDir, ensureSlash]",
            [$this->app->make('files'), $this->app->make("module.service.manager"), $routeSource]
        );
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        $routeSource->shouldReceive("getConsoleRoute")->andReturn($consoleRoute = "console_route");
        $routeSource->shouldReceive("getRouteFileExtension")->andReturn($extension = ".kt");

        $factory->shouldReceive("getRelativeRouteRootDir")->withArgs([$module])->andReturn($path = "path/to/thing");
        $factory->shouldReceive("ensureSlash")->withArgs([$path])->andReturn("$path/");

        $expected = "$path/$consoleRoute$extension";
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }

    public function testGetRelativeRouteRootDir(): void
    {
        $routeSource = Mockery::mock(RouteSource::class);

        $uut = $this->getMethodFromClass("getRelativeRouteRootDir", ConsoleKernelFactory::class);
        $factory = Mockery::mock(
            ConsoleKernelFactory::class . "[getConsoleDirectory]",
            [$this->app->make('files'), $this->app->make("module.service.manager"), $routeSource]
        );
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "root");
        $routeSource->shouldReceive("getRouteRootDir")->andReturn($dir = "route_dir");

        $expected = "$root/$module/$dir";
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }

    public function testGetKernelNamespace(): void
    {
        $moduleManager = Mockery::mock(ModuleManager::class);

        $uut = $this->getMethodFromClass("getKernelNamespace", ConsoleKernelFactory::class);
        $factory = Mockery::mock(
            ConsoleKernelFactory::class . "[getConsoleDirectory]",
            [$this->app->make('files'), $moduleManager, $this->app->make('module.service.route_source')]
        );
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        $moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->andReturn(
            $namespace = "Modules\\$module\\"
        );

        $factory->shouldReceive("getConsoleDirectory")->andReturn($dir = "directory");

        $this->assertSame("$namespace$dir", $uut->invoke($factory, $module));
    }

    public function testGetModuleKernel(): void
    {
        $uut = $this->getMethodFromClass("getModuleKernel", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $this->assertSame(Kernel::class, $uut->invoke($factory));
    }

    public function testGetKernelConsoleDir(): void
    {
        $uut = $this->getMethodFromClass("getKernelConsoleDir", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $module = "NewModule";

        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "root");
        $expected = "$root/$module/Console/Commands";
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }

    public function testGetStub(): void
    {
        $uut = $this->getMethodFromClass("getStub", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $stub = realpath(__DIR__ . "/../../src/Factories/stubs/consoleKernel.stub");
        $this->assertSame($stub, $uut->invoke($factory));
    }

    public function testGetKernelNamespacePlaceholder(): void
    {
        $uut = $this->getMethodFromClass("getKernelNamespacePlaceholder", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $this->assertSame("{kernelNamespace}", $uut->invoke($factory));
    }

    public function testGetModuleKernelPlaceholder(): void
    {
        $uut = $this->getMethodFromClass("getModuleKernelPlaceholder", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $this->assertSame("{moduleKernel}", $uut->invoke($factory));
    }

    public function testGetKernelConsoleDirPlaceholder(): void
    {
        $uut = $this->getMethodFromClass("getKernelConsoleDirPlaceholder", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $this->assertSame("{kernelConsolePath}", $uut->invoke($factory));
    }

    public function testGetRouteFilePlaceholder(): void
    {
        $uut = $this->getMethodFromClass("getRouteFilePlaceholder", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $this->assertSame("{kernelConsoleRouteFile}", $uut->invoke($factory));
    }

    public function testGetConsoleDirectory(): void
    {
        $uut = $this->getMethodFromClass("getConsoleDirectory", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $this->assertSame("Console", $uut->invoke($factory));
    }

    public function testGetKernelFileName(): void
    {
        $uut = $this->getMethodFromClass("getKernelFileName", ConsoleKernelFactory::class);
        $factory = $this->app->make("module.factory.console_kernel");
        $this->assertSame("Kernel.php", $uut->invoke($factory));
    }
}
