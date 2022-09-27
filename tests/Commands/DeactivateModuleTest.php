<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

class DeactivateModuleTest extends CommandTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
    }

    public function testDeactivateModule(): void
    {
        // I have a module I want to deactivate
        $module = "activeModule";

        $response = $this->artisan("module:deactivate", ["name" => $module]);

        // My modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // The module should exist
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);
        // And the module should be active
        $this->moduleManager->shouldReceive("moduleIsActive")->withArgs([$module])->andReturn(true);
        // Then is should be deactivated
        $this->moduleManager->shouldReceive("deactivateModule")->withArgs([$module]);

        // And I want to get some feedback
        $response->expectsOutput("The module \"$module\" has been deactivated.");
        $response->run();
    }

    public function testModulesAreNotInitialised(): void
    {
        // I have a module I want to deactivate
        $module = "activeModule";

        $response = $this->artisan("module:deactivate", ["name" => $module]);

        // If my modules are not initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(false);

        // I want to get some feedback
        $response->expectsOutput(
            "The modules need to be initialised first. You can do this by running the module:init command."
        );
        $response->run();
    }

    public function testModuleDoesNotExist(): void
    {
        // I have a module I want to deactivate
        $module = "activeModule";

        $response = $this->artisan("module:deactivate", ["name" => $module]);

        // My modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // But the module does not exist
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);

        // I want to get some feedback
        $response->expectsOutput("There is no module named \"$module\".");
        $response->run();
    }

    public function testModuleAlreadyInactivate(): void
    {
        // I have a module I want to deactivate
        $module = "activeModule";

        $response = $this->artisan("module:deactivate", ["name" => $module]);

        // My modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // The module should exist
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);
        // And the module should be active
        $this->moduleManager->shouldReceive("moduleIsActive")->withArgs([$module])->andReturn(false);

        // I want to get some feedback
        $response->expectsOutput("The module \"$module\" is already deactivated.");
        $response->run();
    }
}
