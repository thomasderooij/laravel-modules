<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class DeactivateModuleCommandTest extends CommandTest
{
    public function testDeactivateModule () : void
    {
        // I want to deactivate a module
        $module = "ActiveModule";
        $strtolowModule = strtolower($module);
        $response = $this->artisan("module:deactivate", ["name" => $strtolowModule]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");
        // With a cache driver
        Cache::shouldReceive("driver")->andReturn(new Repository(new FileStore($this->app['files'], base_path("storage/cache"))))->once();

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        // Get its contents, and remove the active module
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => [$module, "OtherModule"], "activeModules" => [$module]])
        );
        // Which should be replaced with one that has added the module to the active key
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path($root)])->andReturn(true);
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/.tracker"), json_encode([
            "modules" => [$module, "OtherModule"],
            "activeModules" => []
        ], JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES)]);
        // And the module should be removed from the workbench
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $module]);
        Cache::shouldReceive("put")->withArgs(["modules-cache", ["workbench" => null], 604800]);

        // See if the module directory exists
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path("$root")])->andReturn(true);

        // And I expect some feedback
        $response->expectsOutput("The module \"$strtolowModule\" has been deactivated.");

        $response->run();
    }

    public function testModulesAreNotInitialised () : void
    {
        // I want to deactivate a module
        $module = "ActiveModule";
        $strtolowModule = strtolower($module);
        $response = $this->artisan("module:deactivate", ["name" => $strtolowModule]);

        // The configuration should not exist
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = null);
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should not have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(false);

        // I want to get some feedback
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }

    public function testModuleDoesNotExist () : void
    {
        // I want to deactivate a module
        $module = "ActiveModule";
        $response = $this->artisan("module:deactivate", ["name" => $module]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => ["WrongModule"], "activeModules" => []])
        );

        // Get info stating the module doesn't exist
        $response->expectsOutput("There is no module named \"$module\".");
        $response->run();
    }

    public function testModuleAlreadyInactivate () : void
    {
        // I want to deactivate a module
        $module = "ActiveModule";
        $response = $this->artisan("module:deactivate", ["name" => $module]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => [$module], "activeModules" => []])
        );

        // I want to get some feedback
        $response->expectsOutput("The module \"$module\" is already deactivated.");
        $response->run();
    }
}
