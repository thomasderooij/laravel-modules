<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CheckWorkbenchCommandTest extends CommandTest
{
    public function testCheckWorkbenchIsEmpty(): void
    {
        $response = $this->artisan("module:check");

        // The configuration should know its root
        $root = "Root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn("Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");
        // And there should be a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $module = "SomeModule";
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode([
                "modules" => [$module],
                "activeModules" => [$module]
            ])
        );

        // If I don't a module in my cache
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // I should be told my workbench is empty
        $response->expectsOutput("Your workbench is empty.");
        $response->run();
    }

    public function testCheckWorkbenchContainsAModule(): void
    {
        $response = $this->artisan("module:check");

        // The configuration should know its root
        $root = "Root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");
        // And there should be a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $module = "SomeModule";
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode([
                "modules" => [$module],
                "activeModules" => [$module]
            ])
        );

        // If I don't a module in my cache
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => "SomeModule"]);

        // I should be told my workbench is empty
        $response->expectsOutput("SomeModule");
        $response->run();
    }

    public function testModulesNotInitialised(): void
    {
        $response = $this->artisan("module:check");

        // The configuration should know its root
        $root = "Root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn(null);
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        $response->expectsOutput(
            "The modules need to be initialised first. You can do this by running the module:init command."
        );
        $response->run();
    }
}
