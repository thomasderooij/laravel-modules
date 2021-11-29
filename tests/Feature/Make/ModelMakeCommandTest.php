<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Database\Factories\HasFactory;

class ModelMakeCommandTest extends MakeTest
{
    public function testWithoutModule () : void
    {
        // If I want to make a model for my module
        $response = $this->artisan("make:model", ["name" => $model = "MyNewModel"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn($fileDirectory = "Aggregates");
        Config::shouldReceive("get")->withArgs(["modules.has_factory_trait", null])->andReturn(HasFactory::class);
        Config::shouldReceive("get")->withArgs(["modules.base_model", null])->andReturn(Model::class);

        // We should then check if this model already exists
        $fileName = "$model.php";
        $this->setFileExpectations($fileDirectory, $fileName, false);

        // The model stub should be fetched
        $this->fetchStub();

        // The model should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("app/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithModule () : void
    {
        // If I want to make a model for my module
        // The casing of the module name differs from the one in the tracker file to ensure casing does not matter for the module option
        $response = $this->artisan("make:model", ["name" => $model = "MyNewModel", "--module" => $module = "MyModule"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn($fileDirectory = "Aggregates");
        Config::shouldReceive("get")->withArgs(["modules.has_factory_trait", null])->andReturn(HasFactory::class);
        Config::shouldReceive("get")->withArgs(["modules.base_model", null])->andReturn(Model::class);

        // We should then check if this model already exists
        $fileName = "$model.php";
        $this->setFileExpectations($fileDirectory, $fileName, true);

        // The model stub should be fetched
        $this->fetchStub();

        // The model should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithWorkbench () : void
    {
        // If I want to make a model for my module
        $response = $this->artisan("make:model", ["name" => $model = "MyNewModel"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $this->module]);
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn($fileDirectory = "Aggregates");
        Config::shouldReceive("get")->withArgs(["modules.has_factory_trait", null])->andReturn(HasFactory::class);
        Config::shouldReceive("get")->withArgs(["modules.base_model", null])->andReturn(Model::class);

        // We should then check if this model already exists
        $fileName = "$model.php";
        $this->setFileExpectations($fileDirectory, $fileName, true);

        // The model stub should be fetched
        $this->fetchStub();

        // The model should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithVanillaModule () : void
    {
        // If I want to make a model for my module
        $response = $this->artisan("make:model", ["name" => $model = "MyNewModel", "--module" => $module = "MYMODULE"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule($module);

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn($fileDirectory = "Aggregates");
        Config::shouldReceive("get")->withArgs(["modules.has_factory_trait", null])->andReturn(HasFactory::class);
        Config::shouldReceive("get")->withArgs(["modules.base_model", null])->andReturn(Model::class);

        // We should then check if this model already exists
        $fileName = "$model.php";
        $this->setFileExpectations($fileDirectory, $fileName, false);

        // The model stub should be fetched
        $this->fetchStub();

        // The model should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("app/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    protected function fetchStub(): void
    {
        $stub = realpath(__DIR__ . "/../../../src/Factories/stubs/model.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$stub])->andReturn($this->files->get($stub));
    }
}
