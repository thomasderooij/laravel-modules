<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Factories\ModuleFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class NewModuleCommandTest extends Test
{
    private $moduleManager;
    private $moduleFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleFactory = Mockery::mock(ModuleFactory::class);
        $this->instance("module.factory.module", $this->moduleFactory);
        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
    }

    public function testMakeNewModule () : void
    {
        $newModule = "NewModule";
        $response = $this->artisan("module:new", ["name" => $newModule]);

        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$newModule])->andReturn(false);
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);

        $this->moduleFactory->shouldReceive("create")->withArgs([$newModule]);
        $this->moduleManager->shouldReceive("addModule")->withArgs([$newModule]);
        $this->moduleManager->shouldReceive("setWorkbench")->withArgs([$newModule]);

        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn("file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");

        $response->expectsOutput("Your module has been created in the $root/$newModule directory.");
        $response->run();
    }

    public function testModulesNotInitialised () : void
    {
        $newModule = "NewModule";
        $response = $this->artisan("module:new", ["name" => $newModule]);

        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(false);
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }

    public function testModuleAlreadyExists () : void
    {
        $newModule = "NewModule";
        $response = $this->artisan("module:new", ["name" => $newModule]);

        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$newModule])->andReturn(true);

        $response->expectsOutput("Module $newModule already exists.");
        $response->run();
    }
}
