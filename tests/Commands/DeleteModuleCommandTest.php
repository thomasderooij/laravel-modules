<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

class DeleteModuleCommandTest extends CommandTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
    }

    public function testDeleteModule () : void
    {
        // If I want to delete a module
        $module = "ExistingModule";
        $response = $this->artisan("module:delete", ["name" => $module]);

        // The modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // And the module should exist
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);

        // I should be asked to confirm if I want to delete the module directory, and I confirm
        $response->expectsChoice("This will delete your module \"$module\" and all of the code within it. Are you sure you want to do this?", "Yes, I'm sure", [
            1 => "Yes, I'm sure",
            0 => "No, I don't want to delete everything",
        ]);

        // The module manager deletes the module directory
        $this->moduleManager->shouldReceive("removeModule")->withArgs([$module]);

        // And I expect some feedback
        $response->expectsOutput("Aaaaaand it's gone.");
        $response->run();
    }

    public function testCancelDeleteModule () : void
    {
        // If I want to delete a module
        $module = "ExistingModule";
        $response = $this->artisan("module:delete", ["name" => $module]);

        // The modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // And the module should exist
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);

        // I should be asked to confirm if I want to delete the module directory, and I chicken out
        $response->expectsChoice("This will delete your module \"$module\" and all of the code within it. Are you sure you want to do this?", "No, I don't want to delete everything", [
            1 => "Yes, I'm sure",
            0 => "No, I don't want to delete everything",
        ]);

        // And I expect some feedback
        $response->expectsOutput("Gotcha. I'll leave your code intact.");
        $response->run();
    }

    public function testIsNotInitialised () : void
    {
        // If I want to delete a module
        $module = "ExistingModule";
        $response = $this->artisan("module:delete", ["name" => $module]);

        // The modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(false);

        // I want to get some feedback
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }

    public function testModuleDoesNotExist (): void
    {
        // If I want to delete a module
        $module = "ExistingModule";
        $response = $this->artisan("module:delete", ["name" => $module]);

        // My modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // But the module does not exist
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);

        // I want to get some feedback
        $response->expectsOutput("There is no module named \"$module\".");
        $response->run();
    }
}
