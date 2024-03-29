<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SetWorkbenchCommandTest extends CommandTest
{
    public function testSetWorkbench(): void
    {
        // If I want to set a module to my workbench
        $module = "CurrentModule";
        $strtolower = strtolower($module);
        $response = $this->artisan("module:set", ["name" => $strtolower]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");
        Config::shouldReceive('get')->withArgs(['modules.cache_validity', null])->andReturn($validity = 123);
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => null]);


        // We should get the tracker file and its content, to see if the given module matches
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])
            ->andReturn(json_encode(["modules" => [$module], "activeModules" => []]));

        // The module should be put in the workbench
        Cache::shouldReceive("put")->withArgs(["modules-cache", ["workbench" => $module], $validity]);

        // And I should get some feedback
        $response->expectsOutput("The module \"$strtolower\" is now set to your workbench.");
        $response->run();
    }

    public function testModulesAreNotInitialised(): void
    {
        // If I want to set a module to my workbench
        $module = "CurrentModule";
        $strtolower = strtolower($module);
        $response = $this->artisan("module:set", ["name" => $strtolower]);

        // The configuration should not exist
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = null);
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should not have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(false);

        // I want to get some feedback
        $response->expectsOutput(
            "The modules need to be initialised first. You can do this by running the module:init command."
        );
        $response->run();
    }

    public function testModuleDoesNotExist(): void
    {
        // If I want to set a module to my workbench
        $module = "CurrentModule";
        $strtolower = strtolower($module);
        $response = $this->artisan("module:set", ["name" => $strtolower]);// The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => ["WrongModule"], "activeModules" => []])
        );

        // Get info stating the module doesn't exist
        $response->expectsOutput("There is no module named \"$strtolower\".");
        $response->run();
    }
}
