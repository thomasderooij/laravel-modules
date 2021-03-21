<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery;

class FactoryMakeCommandTest extends MakeTest
{
    public function testWithoutModule () : void
    {
        // If I want to make a factory for my module
        $response = $this->artisan("make:factory", ["name" => $factory = "MyNewFactory"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn("Aggregates");

        // We should then check if this factory already exists
        $fileDirectory = "database/factories";
        $fileName = "$factory.php";
        $this->setFileExpectations(null, $fileName, false,$fileDirectory);

        // The factory stub should be fetched
        $this->fetchStub();

        // The factory should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithModule () : void
    {
        // If I want to make a factory for my module
        // The casing of the module name differs from the one in the tracker file to ensure casing does not matter for the module option
        $response = $this->artisan("make:factory", ["name" => $factory = "MyNewFactory", "--module" => $module = "MyModule"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn("Aggregates");

        // We should then check if this factory already exists
        $fileDirectory = "Database/Factories";
        $fileName = "$factory.php";
        $this->setFileExpectations($fileDirectory, $fileName, true);

        // The factory stub should be fetched
        $this->fetchStub();

        // The factory should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithWorkbench () : void
    {
        // If I want to make a factory for my module
        $response = $this->artisan("make:factory", ["name" => $factory = "MyNewFactory"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $this->module]);
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn("Aggregates");

        // We should then check if this factory already exists
        $fileDirectory = "Database/Factories";
        $fileName = "$factory.php";
        $this->setFileExpectations($fileDirectory, $fileName, true);

        // The factory stub should be fetched
        $this->fetchStub();

        // The factory should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithVanillaModule () : void
    {
        // If I want to make a factory for my module
        $response = $this->artisan("make:factory", ["name" => $factory = "MyNewFactory", "--module" => $module = "Vanilla"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule($module);

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        Config::shouldReceive("get")->withArgs(["modules.vanilla", null])->andReturn($module);
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn("Aggregates");

        // We should then check if this factory already exists
        $fileDirectory = "database/factories";
        $fileName = "$factory.php";
        $this->setFileExpectations(null, $fileName, false, $fileDirectory);

        // The factory stub should be fetched
        $this->fetchStub();

        // The factory should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    protected function fetchStub(): void
    {
        $stub = realpath(__DIR__ . "/../../../vendor/laravel/framework/src/Illuminate/Database/Console/Factories/stubs/factory.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$stub])->andReturn($this->files->get($stub));
    }
}
