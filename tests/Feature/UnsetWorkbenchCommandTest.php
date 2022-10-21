<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class UnsetWorkbenchCommandTest extends CommandTest
{
    public function testUnsetWorkbench(): void
    {
        // If I want to clear my workbench
        $response = $this->artisan("module:unset");

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");
        Config::shouldReceive('get')->withArgs(['modules.cache_validity', null])->andReturn($validity = 123);
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => null]);

        // The workbench should be cleared
        Cache::shouldReceive("put")->withArgs(["modules-cache", ["workbench" => null], $validity]);
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])
            ->andReturn(json_encode(["modules" => ["SomeModule"], "activeModules" => []]));

        // And I should get some feedback
        $response->expectsOutput("Your workbench has been cleared.");
        $response->run();
    }

    public function testModulesAreNotInitialised(): void
    {
        // If I want to clear my workbench
        $response = $this->artisan("module:unset");

        // The configuration should not exist
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = null);
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should not have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(false);

        // And I should get some feedback
        $response->expectsOutput(
            "The modules need to be initialised first. You can do this by running the module:init command."
        );
        $response->run();
    }
}
