<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Support\Facades\Cache;
use Mockery;

class ExceptionMakeCommandTest extends MakeTest
{
    /**
     * @group uut
     */
    public function testWithoutModule () : void
    {
        // If I want to make a exception for my module
        $response = $this->artisan("make:exception", ["name" => $exception = "MyNewException"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this exception already exists
        $fileDirectory = "Exceptions";
        $fileName = "$exception.php";
        $this->setFileExpectations($fileDirectory, $fileName, false, null, false);

        // The exception stub should be fetched
        $this->fetchStub();

        // The exception should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("app/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithModule () : void
    {
        // If I want to make a exception for my module
        // The casing of the module name differs from the one in the tracker file to ensure casing does not matter for the module option
        // If I want to make a exception for my module
        $response = $this->artisan("make:exception", ["name" => $exception = "MyNewException", "--module" => $module = "MyModule"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this exception already exists
        $fileDirectory = "Exceptions";
        $fileName = "$exception.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null, false);

        // The exception stub should be fetched
        $this->fetchStub();

        // The exception should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithWorkbench () : void
    {
        // If I want to make a exception for my module
        $response = $this->artisan("make:exception", ["name" => $exception = "MyNewException"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $this->module]);

        // We should then check if this exception already exists
        $fileDirectory = "Exceptions";
        $fileName = "$exception.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null, false);

        // The exception stub should be fetched
        $this->fetchStub();

        // The exception should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithVanillaModule () : void
    {
        // If I want to make a exception for my module
        $response = $this->artisan("make:exception", ["name" => $exception = "MyNewException", "--module" => $module = "MYMODULE"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule($module);

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this exception already exists
        $fileDirectory = "Exceptions";
        $fileName = "$exception.php";
        $this->setFileExpectations($fileDirectory, $fileName, false, null, false);

        // The exception stub should be fetched
        $this->fetchStub();

        // The exception should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("app/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    protected function fetchStub(): void
    {
        $stub = realpath(__DIR__ . "/../../../vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/exception.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$stub])->andReturn($this->files->get($stub));
    }
}
