<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Support\Facades\Cache;
use Mockery;

class TestMakeCommandTest extends MakeTest
{
    public function testWithoutModule () : void
    {
        // If I want to make a test for my module
        $response = $this->artisan("make:test", ["name" => $test = "MyNewTest"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this test already exists
        $fileDirectory = "tests/Feature";
        $fileName = "$test.php";
        $this->setFileExpectations(null, $fileName, false,$fileDirectory);

        // The test stub should be fetched
        $this->fetchStub();

        // The test should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithModule () : void
    {
        // If I want to make a test for my module
        // The casing of the module name differs from the one in the tracker file to ensure casing does not matter for the module option
        $response = $this->artisan("make:test", ["name" => $test = "MyNewTest", "--module" => $module = "MyModule"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this test already exists
        $fileDirectory = "Tests/Feature";
        $fileName = "$test.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null);

        // The test stub should be fetched
        $this->fetchStub();

        // The test should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithWorkbench () : void
    {
        // If I want to make a test for my module
        $response = $this->artisan("make:test", ["name" => $test = "MyNewTest"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $this->module]);

        // We should then check if this test already exists
        $fileDirectory = "Tests/Feature";
        $fileName = "$test.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null);

        // The test stub should be fetched
        $this->fetchStub();

        // The test should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithVanillaModule () : void
    {
        // If I want to make a test for my module
        $response = $this->artisan("make:test", ["name" => $test = "MyNewTest", "--module" => $module = "MYMODULE"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule($module);

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this test already exists
        $fileDirectory = "tests/Feature";
        $fileName = "$test.php";
        $this->setFileExpectations(null, $fileName, false, $fileDirectory);

        // The test stub should be fetched
        $this->fetchStub();

        // The test should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    protected function fetchStub(): void
    {
        $stub = realpath(__DIR__ . "/../../../vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/test.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$stub])->andReturn($this->files->get($stub));
    }
}
