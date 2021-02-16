<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Cache;
use Mockery;

class MigrateMakeCommandTest extends MakeTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $composer = Mockery::mock(Composer::class);
        $this->instance("composer", $composer);

        // Autoloads should be dumped
        $this->filesystem->shouldReceive("exists")->withArgs([realpath(__DIR__ . "/../../../vendor/orchestra/testbench-core/laravel") . "/composer.phar"])->andReturn(false);
        $composer->shouldReceive("dumpAutoloads");

        // A stub should exist
        $orchestraPath = realpath(__DIR__ . "/../../../vendor/orchestra/testbench-core/laravel") . "/stubs/migration.create.stub";
        $this->filesystem->shouldReceive("exists")->withArgs([$orchestraPath])->andReturn(true);
    }

    public function testWithoutModule () : void
    {
        // If I want to make a factory for my module
        $response = $this->artisan("make:migration", ["name" => $migration = "create_new_model_table"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this factory already exists
        $fileDirectory = "database/migrations";
        $fileName = "$migration.php";
        $this->setFileExpectations(null, $fileName, false,$fileDirectory, false);
        $this->filesystem->shouldReceive("ensureDirectoryExists")->withArgs([base_path("$fileDirectory")]);
        $this->filesystem->shouldReceive("glob")->withArgs([base_path("$fileDirectory/*.php")])->andReturn([
            $migration1 = "migration_1.php",
        ]);
        $this->filesystem->shouldReceive("requireOnce")->withArgs([$migration1]);

        // The factory stub should be fetched
        $this->fetchStub();

        // The factory should then be created
        $capturedContent = null;
        $this->filesystem->shouldReceive("put")->withArgs([Mockery::any(), Mockery::capture($capturedContent)]);

        $response->run();

        $this->assertMatchesSnapshot($capturedContent);
    }

    public function testWithModule () : void
    {
        // If I want to make a factory for my module
        // The casing of the module name differs from the one in the tracker file to ensure casing does not matter for the module option
        $response = $this->artisan("make:migration", ["name" => $migration = "create_new_model_table", "--module" => $module = "MyModule"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this factory already exists
        $fileDirectory = "database/migrations";
        $fileName = "$migration.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null, false);
        $this->filesystem->shouldReceive("ensureDirectoryExists")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory")]);
        $this->filesystem->shouldReceive("glob")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/*.php")])->andReturn([
            $migration1 = "migration_1.php",
        ]);
        $this->filesystem->shouldReceive("requireOnce")->withArgs([$migration1]);
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory"), 0755, true]);

        // The factory stub should be fetched
        $this->fetchStub();

        // The factory should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([Mockery::any(), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithWorkbench () : void
    {
        // If I want to make a factory for my module
        $response = $this->artisan("make:migration", ["name" => $migration = "create_new_model_table"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $this->module]);

        // We should then check if this factory already exists
        $fileDirectory = "database/migrations";
        $fileName = "$migration.php";
        $this->setFileExpectations($fileDirectory, $fileName, true, null, false);
        $this->filesystem->shouldReceive("ensureDirectoryExists")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory")]);
        $this->filesystem->shouldReceive("glob")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/*.php")])->andReturn([
            $migration1 = "migration_1.php",
        ]);
        $this->filesystem->shouldReceive("requireOnce")->withArgs([$migration1]);
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory"), 0755, true]);

        // The factory stub should be fetched
        $this->fetchStub();

        // The factory should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([Mockery::any(), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithVanillaModule () : void
    {
        // If I want to make a factory for my module
        $response = $this->artisan("make:migration", ["name" => $migration = "create_new_model_table", "--module" => $module = "MYMODULE"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule($module);

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this factory already exists
        $fileDirectory = "database/migrations";
        $fileName = "$migration.php";
        $this->setFileExpectations(null, $fileName, false,$fileDirectory, false);
        $this->filesystem->shouldReceive("ensureDirectoryExists")->withArgs([base_path("$fileDirectory")]);
        $this->filesystem->shouldReceive("glob")->withArgs([base_path("$fileDirectory/*.php")])->andReturn([
            $migration1 = "migration_1.php",
        ]);
        $this->filesystem->shouldReceive("requireOnce")->withArgs([$migration1]);

        // The factory stub should be fetched
        $this->fetchStub();

        // The factory should then be created
        $capturedContent = null;
        $this->filesystem->shouldReceive("put")->withArgs([Mockery::any(), Mockery::capture($capturedContent)]);

        $response->run();

        $this->assertMatchesSnapshot($capturedContent);
    }

    protected function fetchStub(): void
    {
        $stub = realpath(__DIR__ . "/../../../vendor/laravel/framework/src/Illuminate/Database/Migrations/stubs/migration.create.stub");
        $orchestraPath = realpath(__DIR__ . "/../../../vendor/orchestra/testbench-core/laravel") . "/stubs/migration.create.stub";
        $this->filesystem->shouldReceive("get")->withArgs([$orchestraPath])->andReturn($this->files->get($stub));
    }
}
