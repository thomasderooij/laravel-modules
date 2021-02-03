<?php

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Tests\Test;

class ActivateModuleCommandTest extends Test
{
    private $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance('files', $this->filesystem);
    }

    public function testActivateModule () : void
    {
        $module = "InactiveModule";
        $strtolowModule = strtolower($module);

        // Case should not matter when activating a module
        $response = $this->artisan("module:activate", ["name" => $strtolowModule]);

        // The config should check for a module root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        // And get other details to start the console application
        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn($driver = "file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");
        // The cache should check the workbench
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        // With a cache driver
        Cache::shouldReceive("driver")->andReturn(new Repository(new FileStore($this->app['files'], base_path("storage/cache"))))->once();
        // And there should be a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])
            ->andReturn(json_encode(["modules" => [$module, "OtherModule"], "activeModules" =>  ["OtherModule"]]));
        // Which should be replaced with one that has added the module to the active key
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path($root)])->andReturn(true);
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/.tracker"), json_encode([
            "modules" => [$module, "OtherModule"],
            "activeModules" => ["OtherModule", $module]
        ], JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES)]);
        $response->expectsOutput("The module \"$strtolowModule\" has been activated and put in your workbench.");

        // And the module should be put in the workbench
        Cache::shouldReceive("put")->withArgs(["modules-cache", ["workbench" => $module], 604800]);

        $response->run();
    }

    public function testModulesNotInitialised () : void
    {
        $module = "InactiveModule";
        // In order to create a new module
        $response = $this->artisan("module:activate", ["name" => $module]);

        // The configuration should know its root
        $root = "Root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn(null);
        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn($driver = "file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(false);

        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }

    public function testModuleAlreadyActive () : void
    {
        $module = "InactiveModule";
        // In order to create a new module
        $response = $this->artisan("module:activate", ["name" => $module]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn($driver = "file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => [$module], "activeModules" => [$module]])
        );

        // Get info stating the module is already active
        $response->expectsOutput("The module \"$module\" is already active.");
        $response->run();
    }

    public function testModuleDoesNotExist () : void
    {
        $module = "InactiveModule";
        // In order to create a new module
        $response = $this->artisan("module:activate", ["name" => $module]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn($driver = "file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => ["WrongModule"], "activeModules" => ["WrongModule"]])
        );

        // Get info stating the module doesn't exist
        $response->expectsOutput("There is no module named \"$module\".");
        $response->run();
    }
}
