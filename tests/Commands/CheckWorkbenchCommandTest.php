<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

class CheckWorkbenchCommandTest extends CommandTest
{
    public function testCheckWorkbenchIsEmpty () : void
    {
        $response = $this->artisan("module:check");

        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // If I don't have a module in my workbench
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
        // I should be told my workbench is empty
        $response->expectsOutput("Your workbench is empty.");
        $response->run();
    }

    public function testCheckWorkbenchContainsAModule () : void
    {
        $response = $this->artisan("module:check");

        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // If I don't have a module in my workbench
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn($module = "WorkbenchModule");
        // I should be told my workbench is empty
        $response->expectsOutput($module);
        $response->run();
    }

    public function testModulesNotInitialised (): void
    {
        $response = $this->artisan("module:check");

        // Get the workbench to change the artisan command descriptions
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn("OtherModule");
        // Check if the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(false);

        // Get info on how to initialise the modules
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }
}
