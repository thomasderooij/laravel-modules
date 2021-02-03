<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

class UnsetWorkbenchCommandTest extends CommandTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
    }

    public function testUnsetWorkbench () : void
    {
        // If I want to clear my workbench
        $response = $this->artisan("module:unset");

        // My modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // And then we clear the workbench
        $this->moduleManager->shouldReceive("clearWorkbench");

        // And I want some feedback
        $response->expectsOutput("Your workbench has been cleared.");
        $response->run();
    }

    public function testModulesAreNotInitialised () : void
    {
        // If I want to clear my workbench
        $response = $this->artisan("module:unset");

        // But my modules are not initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(false);

        // And I should get some feedback
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }
}
