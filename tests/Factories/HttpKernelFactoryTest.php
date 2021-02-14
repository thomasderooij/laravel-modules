<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Factories\HttpKernelFactory;
use Thomasderooij\LaravelModules\Http\CompositeKernel;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class HttpKernelFactoryTest extends Test
{
    public function testCreate () : void
    {
        $moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $moduleManager);

        /** @var Mockery\MockInterface&HttpKernelFactory $factory */
        $factory = Mockery::mock(HttpKernelFactory::class."[" . implode(", ", $this->getMockableClassMethods(HttpKernelFactory::class, "create")) . "]", [
            $this->app->make('files'),
            $this->app->make("module.service.manager"),
            $this->app->make("module.service.route_source")
        ]);
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        $factory->shouldReceive("getHttpDir")->withArgs([$module])->andReturn($httpDir = "ConsoleDir");
        $factory->shouldReceive("getKernelFileName")->andReturn($kernelFileName = "KernelFile");
        $factory->shouldReceive("getStub")->andReturn($stub = "stub");
        $factory->shouldReceive("getKernelNamespacePlaceholder")->andReturn($kernelNsPh = "kernelNsPh");
        $factory->shouldReceive("getKernelNamespace")->andReturn($kernelNs = "kernelNs");
        $factory->shouldReceive("getModuleKernelPlaceholder")->andReturn($mkPh = "mkPh");
        $factory->shouldReceive("getModuleKernel")->andReturn($mk = "mk");
        $factory->shouldReceive("populateFile")->withArgs([$httpDir, $kernelFileName, $stub, [
            $kernelNsPh => $kernelNs,
            $mkPh => $mk
        ]]);

        $factory->create($module);
    }

    public function testGetHttpDir () : void
    {
        $uut = $this->getMethodFromClass("getHttpDir", HttpKernelFactory::class);
        $factory = Mockery::mock(HttpKernelFactory::class."[getHttpDirectory]", [
            $this->app->make('files'),
            $this->app->make("module.service.manager"),
            $this->app->make("module.service.route_source")
        ]);
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "root");
        $factory->shouldReceive("getHttpDirectory")->andReturn($dir = "http_directory");

        $expected = base_path("$root/$module/$dir");
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }

    public function testGetStub () : void
    {
        $uut = $this->getMethodFromClass("getStub", HttpKernelFactory::class);
        $factory = $this->app->make("module.factory.http_kernel");
        $stub = realpath(__DIR__ . "/../../src/Factories/stubs/httpKernel.stub");
        $this->assertSame($stub, $uut->invoke($factory));
    }

    public function testGetKernelNamespacePlaceholder () : void
    {
        $uut = $this->getMethodFromClass("getKernelNamespacePlaceholder", HttpKernelFactory::class);
        $factory = $this->app->make("module.factory.http_kernel");
        $this->assertSame("{kernelNamespace}", $uut->invoke($factory));
    }

    public function testGetKernelNamespace () : void
    {
        $moduleManager = Mockery::mock(ModuleManager::class);

        $uut = $this->getMethodFromClass("getKernelNamespace", HttpKernelFactory::class);
        $factory = Mockery::mock(HttpKernelFactory::class."[getHttpDirectory]", [$this->app->make('files'), $moduleManager, $this->app->make('module.service.route_source')]);
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        $moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->andReturn($namespace = "Modules\\$module\\");

        $factory->shouldReceive("getHttpDirectory")->andReturn($dir = "directory");

        $this->assertSame("$namespace$dir", $uut->invoke($factory, $module));
    }

    public function testGetModuleKernelPlaceholder () : void
    {
        $uut = $this->getMethodFromClass("getModuleKernelPlaceholder", HttpKernelFactory::class);
        $factory = $this->app->make("module.factory.http_kernel");
        $this->assertSame("{moduleKernel}", $uut->invoke($factory));
    }

    public function testGetModuleKernel () : void
    {
        $uut = $this->getMethodFromClass("getModuleKernel", HttpKernelFactory::class);
        $factory = $this->app->make("module.factory.http_kernel");
        $this->assertSame(CompositeKernel::class, $uut->invoke($factory));
    }

    public function testGetHttpDirectory () : void
    {
        $uut = $this->getMethodFromClass("getHttpDirectory", HttpKernelFactory::class);
        $factory = $this->app->make("module.factory.http_kernel");
        $this->assertSame("Http", $uut->invoke($factory));
    }

    public function testGetKernelFileName () : void
    {
        $uut = $this->getMethodFromClass("getKernelFileName", HttpKernelFactory::class);
        $factory = $this->app->make("module.factory.http_kernel");
        $this->assertSame("Kernel.php", $uut->invoke($factory));
    }
}
