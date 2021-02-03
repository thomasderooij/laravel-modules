<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

class SetWorkbenchCommandTest extends CommandTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
    }

    public function testSetWorkbench () : void
    {
        // If I want to set a module to my workbench
        $module = "CurrentModule";
        $response = $this->artisan("module:set", ["name" => $module]);

        // My modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // And the module should exist
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);
        // And then we set the module to the workbench
        $this->moduleManager->shouldReceive("setWorkbench")->withArgs([$module]);

        // And I should get some feedback
        $response->expectsOutput("The module \"$module\" is now set to your workbench.");
        $response->run();
    }

    public function testModulesAreNotInitialised () : void
    {
        // If I want to set a module to my workbench
        $module = "CurrentModule";
        $response = $this->artisan("module:set", ["name" => $module]);

        // But my modules are not initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(false);

        // And I should get some feedback
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }

    public function testModuleDoesNotExist () : void
    {
        // If I want to set a module to my workbench
        $module = "CurrentModule";
        $response = $this->artisan("module:set", ["name" => $module]);

        // My modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // But the module does not exist
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);

        // And I should get some feedback
        $response->expectsOutput("There is no module named \"$module\".");
        $response->run();
    }
}
