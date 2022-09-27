<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery;

class ListenerMakeCommandTest extends MakeTest
{
    public function setUp(): void
    {
        parent::setUp();

        Config::shouldReceive("get")->withArgs(["logging.channels.deprecations"]);
        Config::shouldReceive("set")->withArgs(["logging.channels.deprecations", null]);
        Config::shouldReceive("offsetGet")->withArgs(["logging.channels.emergency"]);
        Config::shouldReceive("get")->withArgs(["logging.deprecations"]);
        Config::shouldReceive("get")->withArgs(["logging.channels.null"]);
        Config::shouldReceive("set")->withArgs(
            ['logging.channels.null', ['driver' => 'monolog', 'handler' => 'Monolog\Handler\NullHandler']]
        );
    }

    public function testWithoutModule(): void
    {
        // If I want to make a listener for my module
        $response = $this->artisan("make:listener", ["name" => $listener = "MyNewListener"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        // We should then check if this listener already exists
        $fileDirectory = "Listeners";
        $fileName = "$listener.php";
        $this->setFileExpectations($fileDirectory, $fileName, false, null, false);

        // The listener stub should be fetched
        $this->fetchStub();

        // The listener should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("app/$fileDirectory/$fileName"), Mockery::capture($capture)]
        );

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithModule(): void
    {
        // If I want to make a listener for my module
        // The casing of the module name differs from the one in the tracker file to ensure casing does not matter for the module option
        $response = $this->artisan(
            "make:listener",
            ["name" => $listener = "MyNewListener", "--module" => $module = "MyModule"]
        );

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this listener already exists
        $fileDirectory = "Listeners";
        $fileName = "$listener.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null, false);

        // The listener stub should be fetched
        $this->fetchStub();

        // The listener should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]
        );

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithWorkbench(): void
    {
        // If I want to make a listener for my module
        $response = $this->artisan("make:listener", ["name" => $listener = "MyNewListener"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $this->module]);

        // We should then check if this listener already exists
        $fileDirectory = "Listeners";
        $fileName = "$listener.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null, false);

        // The listener stub should be fetched
        $this->fetchStub();

        // The listener should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]
        );

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithVanillaModule(): void
    {
        // If I want to make a listener for my module
        $response = $this->artisan(
            "make:listener",
            ["name" => $listener = "MyNewListener", "--module" => $module = "MYMODULE"]
        );

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule($module);

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this listener already exists
        $fileDirectory = "Listeners";
        $fileName = "$listener.php";
        $this->setFileExpectations($fileDirectory, $fileName, false, null, false);

        // The listener stub should be fetched
        $this->fetchStub();

        // The listener should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("app/$fileDirectory/$fileName"), Mockery::capture($capture)]
        );

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    protected function fetchStub(): void
    {
        $stub = realpath(
            __DIR__ . "/../../../vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/listener-duck.stub"
        );
        $this->filesystem->shouldReceive("get")->withArgs([$stub])->andReturn($this->files->get($stub));
    }
}
