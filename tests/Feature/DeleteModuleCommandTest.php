<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class DeleteModuleCommandTest extends CommandTest
{
    public function testDeleteModule(): void
    {
        // If I want to delete a module
        $module = "ExistingModule";
        $lowerModule = strtolower($module);
        $response = $this->artisan("module:delete", ["name" => $lowerModule]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");
        Config::shouldReceive('get')->withArgs(['modules.cache_validity', null])->andReturn($validity = 123);

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => [$module], "activeModules" => []])
        );

        // I should be asked to confirm if I want to delete the module directory, and I confirm
        $response->expectsChoice(
            "This will delete your module \"$lowerModule\" and all of the code within it. Are you sure you want to do this?",
            "Yes, I'm sure",
            [
                1 => "Yes, I'm sure",
                0 => "No, I don't want to delete everything",
            ]
        );

        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $module]);
        // Since the module is in the workbench, the workbench should be cleared
        Cache::shouldReceive("put")->withArgs(["modules-cache", ["workbench" => null], $validity]);
        // We check the modules root
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path("$root")])->andReturn(true);
        // We remove the module from the tracker file
        $this->filesystem->shouldReceive("put")->withArgs([
            base_path("$root/.tracker"),
            json_encode(
                ["modules" => [], "activeModules" => []],
                JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT
            )
        ]);
        $this->filesystem->shouldReceive("deleteDirectories")->withArgs([base_path("$root/$module")]);
        $this->filesystem->shouldReceive("deleteDirectory")->withArgs([base_path("$root/$module")]);

        // And I expect some feedback
        $response->expectsOutput("Aaaaaand it's gone.");
        $response->run();
    }

    public function testCancelDeleteModule(): void
    {
        // If I want to delete a module
        $module = "ExistingModule";
        $response = $this->artisan("module:delete", ["name" => $module]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => [$module], "activeModules" => []])
        );

        // I should be asked to confirm if I want to delete the module directory, and I chicken out
        $response->expectsChoice(
            "This will delete your module \"$module\" and all of the code within it. Are you sure you want to do this?",
            "No, I don't want to delete everything",
            [
                1 => "Yes, I'm sure",
                0 => "No, I don't want to delete everything",
            ]
        );

        // And I expect some feedback
        $response->expectsOutput("Gotcha. I'll leave your code intact.");
        $response->run();
    }

    public function testIsNotInitialised(): void
    {
        // If I want to delete a module
        $module = "ExistingModule";
        $response = $this->artisan("module:delete", ["name" => $module]);

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
        // If I want to delete a module
        $module = "ExistingModule";
        $response = $this->artisan("module:delete", ["name" => $module]);

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
}
