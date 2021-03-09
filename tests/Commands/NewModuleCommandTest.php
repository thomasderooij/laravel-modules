<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Factories\ModuleFactory;
use Thomasderooij\LaravelModules\Services\DependencyHandler;

class NewModuleCommandTest extends CommandTest
{
    private $moduleFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleFactory = Mockery::mock(ModuleFactory::class);
        $this->instance("module.factory.module", $this->moduleFactory);

        // This is for the kernels
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
    }

    public function testMakeNewModule () : void
    {
        // When I make a new module
        $newModule = "NewModule";
        $response = $this->artisan("module:new", ["name" => $newModule]);

        // Modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // And we should not have this module yet
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$newModule])->andReturn(false);

        // We then call create, add, and set the module to the workbench
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

        // We then expect to be told where we can find our brand new module
        $response->expectsOutput("Your module has been created in the $root/$newModule directory.");

        $response->run();
    }

    public function testModulesNotInitialised () : void
    {
        $newModule = "NewModule";
        $response = $this->artisan("module:new", ["name" => $newModule]);

        $this->moduleManager->shouldReceive("isInitialised")->andReturn(false);
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }

    public function testModuleAlreadyExists () : void
    {
        $newModule = "NewModule";
        $response = $this->artisan("module:new", ["name" => $newModule]);

        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$newModule])->andReturn(true);

        $response->expectsOutput("Module $newModule already exists.");
        $response->run();
    }
}
