<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Cache;
use Mockery;

class SeederMakeCommandTest extends MakeTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $composer = Mockery::mock(Composer::class);
        $this->instance("composer", $composer);

        // Autoloads should be dumped
        $this->filesystem->shouldReceive("exists")->withArgs([realpath(__DIR__ . "/../../../vendor/orchestra/testbench-core/laravel") . "/composer.phar"])->andReturn(false);
        $composer->shouldReceive("dumpAutoloads");

    }

    public function testWithoutModule () : void
    {
        // If I want to make a seeder for my module
        $response = $this->artisan("make:seeder", ["name" => $seeder = "MyNewSeeder"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this seeder already exists
        $fileDirectory = "database/seeders";
        $fileName = "$seeder.php";
        $this->setFileExpectations(null, $fileName, false,$fileDirectory);

        // The seeder stub should be fetched
        $this->fetchStub();

        // The seeder should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithModule () : void
    {
        // If I want to make a seeder for my module
        // The casing of the module name differs from the one in the tracker file to ensure casing does not matter for the module option
        $response = $this->artisan("make:seeder", ["name" => $seeder = "MyNewSeeder", "--module" => $module = "MyModule"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this seeder already exists
        $fileDirectory = "Database/Seeders";
        $fileName = "$seeder.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null);

        // The seeder stub should be fetched
        $this->fetchStub($module);

        // The seeder should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithWorkbench () : void
    {
        // If I want to make a seeder for my module
        $response = $this->artisan("make:seeder", ["name" => $seeder = "MyNewSeeder"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $this->module]);

        // We should then check if this seeder already exists
        $fileDirectory = "Database/Seeders";
        $fileName = "$seeder.php";
        $this->setFileExpectations($fileDirectory, $fileName, true);

        // The seeder stub should be fetched
        $this->fetchStub($this->module);

        // The seeder should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithVanillaModule () : void
    {
        // If I want to make a seeder for my module
        $response = $this->artisan("make:seeder", ["name" => $seeder = "MyNewSeeder", "--module" => $module = "MYMODULE"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule($module);

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this seeder already exists
        $fileDirectory = "database/seeders";
        $fileName = "$seeder.php";
        $this->setFileExpectations(null, $fileName, false, $fileDirectory);

        // The seeder stub should be fetched
        $this->fetchStub();

        // The seeder should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    protected function fetchStub(string $module = null): void
    {
        if ($module === null) {
            $stub = realpath(__DIR__ . "/../../../vendor/laravel/framework/src/Illuminate/Database/Console/Seeds/stubs/seeder.stub");
        } else {
            $stub = realpath(__DIR__ . "/../../../src/Factories/stubs/seeder.stub");
        }

        $this->filesystem->shouldReceive("get")->withArgs([$stub])->andReturn($this->files->get($stub));
    }
}
