<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Mockery;
use Thomasderooij\LaravelModules\Factories\ControllerFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class ControllerFactoryTest extends Test
{
    public function testCreate(): void
    {
        /** @var Mockery\MockInterface&ControllerFactory $factory */
        $factory = Mockery::mock(
            ControllerFactory::class . "[populateFile, getDir, getFileName, getStub, getNamespacePlaceholder, getNamespace, getClassNamePlaceHolder, getClassName]",
            [$this->app->make("files"), $this->app->make("module.service.manager")]
        );
        $factory->shouldAllowMockingProtectedMethods();

        $module = "Module";
        $factory->shouldReceive("getDir")->withArgs([$module])->andReturn($dir = "dir");
        $factory->shouldReceive("getFileName")->andReturn($fileName = "fileName");
        $factory->shouldReceive("getStub")->andReturn($stub = "stub");
        $factory->shouldReceive("getNamespacePlaceholder")->andReturn($nsPlaceholder = "nsPlaceholder");
        $factory->shouldReceive("getNamespace")->withArgs([$module])->andReturn($ns = "ns");
        $factory->shouldReceive("getClassNamePlaceHolder")->andReturn($cnPlaceholder = "cnPlaceholder");
        $factory->shouldReceive("getClassName")->andReturn($class = "class");
        $factory->shouldReceive("populateFile")->withArgs([
            $dir,
            $fileName,
            $stub,
            [
                $nsPlaceholder => $ns,
                $cnPlaceholder => $class
            ]
        ]);

        $factory->create($module);
    }

    public function testGetQualifiedClassName(): void
    {
        $uut = $this->getMethodFromClass("getQualifiedClassName", ControllerFactory::class);
        $factory = Mockery::mock(
            ControllerFactory::class . "[getNamespace, getClassName]",
            [$this->app->make("files"), $this->app->make("module.service.manager")]
        );
        $factory->shouldAllowMockingProtectedMethods();

        $module = "NewModule";
        $namespace = "Modules\\NewModule";
        $factory->shouldReceive("getNamespace")->withArgs([$module])->andReturn($namespace);
        $className = "Controllerrr";
        $factory->shouldReceive("getClassName")->andReturn($className);
        $this->assertSame("$namespace\\$className", $uut->invoke($factory, $module));
    }

    public function testGetClassName(): void
    {
        $uut = $this->getMethodFromClass("getClassName", ControllerFactory::class);
        $factory = $this->app->make("module.factory.controller");
        $this->assertSame("Controller", $uut->invoke($factory));
    }

    public function testGetNamespace(): void
    {
        $moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $moduleManager);

        $module = "NewModule";
        $namespace = "Modules\\NewModule";
        $moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->andReturn("$namespace\\");

        $uut = $this->getMethodFromClass("getNamespace", ControllerFactory::class);
        $factory = $this->app->make("module.factory.controller");
        $this->assertSame("$namespace\\Http\\Controllers", $uut->invoke($factory, $module));
    }

    public function testGetClassnamePlaceholder(): void
    {
        $uut = $this->getMethodFromClass("getClassNamePlaceholder", ControllerFactory::class);
        $factory = $this->app->make("module.factory.controller");
        $this->assertSame("{class}", $uut->invoke($factory));
    }

    public function testGetNamespacePlaceholder(): void
    {
        $uut = $this->getMethodFromClass("getNamespacePlaceholder", ControllerFactory::class);
        $factory = $this->app->make("module.factory.controller");
        $this->assertSame("{namespace}", $uut->invoke($factory));
    }

    public function testGetDir(): void
    {
        $moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $moduleManager);

        $module = "NewModule";
        $dir = "Modules/NewModule";
        $moduleManager->shouldReceive("getModuleDirectory")->withArgs([$module])->andReturn($dir);

        $uut = $this->getMethodFromClass("getDir", ControllerFactory::class);
        $factory = $this->app->make("module.factory.controller");
        $this->assertSame("$dir/Http/Controllers", $uut->invoke($factory, $module));
    }

    public function testGetStub(): void
    {
        $uut = $this->getMethodFromClass("getStub", ControllerFactory::class);
        $factory = $this->app->make("module.factory.controller");
        $expected = realpath(__DIR__ . "/../../src/Factories/stubs/controller.stub");
        $this->assertSame($expected, $uut->invoke($factory));
    }

    public function testGetFileName(): void
    {
        $uut = $this->getMethodFromClass("getFileName", ControllerFactory::class);
        $factory = $this->app->make("module.factory.controller");
        $this->assertSame("Controller.php", $uut->invoke($factory));
    }
}
