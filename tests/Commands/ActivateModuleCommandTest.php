<?php

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class ActivateModuleCommandTest extends Test
{
    private $moduleManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
    }

    public function testActivateModule(): void
    {
        $module = "MyModule";

        // If I activate a module
        $response = $this->artisan("module:activate", ["name" => $module]);

        // Get the active modules for the kernel
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        // Check if the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // Check if the module exists
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);
        // Check if the module is already active
        $this->moduleManager->shouldReceive("moduleIsActive")->withArgs([$module])->andReturn(false);
        // Activate the modules if its not active
        $this->moduleManager->shouldReceive("activateModule")->withArgs([$module]);
        // Get the workbench
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
        // Set the module to the workbench if the workbench is empty
        $this->moduleManager->shouldReceive("setWorkbench")->withArgs([$module]);

        // Get feedback
        $response->expectsOutput("The module \"$module\" has been activated and put in your workbench.");
        $response->run();
    }

    public function testActivateModuleWhileWorkbenchIsOccupied () : void
    {
        $module = "MyModule";

        // If I activate a module
        $response = $this->artisan("module:activate", ["name" => $module]);

        // Get the active modules for the kernel
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        // Check if the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // Check if the module is already active
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);
        // Activate the modules if its not active
        $this->moduleManager->shouldReceive("moduleIsActive")->withArgs([$module])->andReturn(false);
        // Activate the modules if its not active
        $this->moduleManager->shouldReceive("activateModule")->withArgs([$module]);
        // Get the workbench
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn("OtherModule");
        // No need to set it to the workbench if we're already working on a different module

        $response->expectsOutput("The module \"$module\" has been activated.");
        $response->run();
    }

    public function testModulesNotInitialised (): void
    {
        $module = "MyModule";

        // If I activate a module
        $response = $this->artisan("module:activate", ["name" => $module]);

        // Get the active modules for the kernel
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        // Get the workbench to change the artisan command descriptions
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn("OtherModule");
        // Check if the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(false);

        // Get info on how to initialise the modules
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }

    public function testModuleAlreadyActive(): void
    {
        $module = "MyModule";

        // If I activate a module
        $response = $this->artisan("module:activate", ["name" => $module]);

        // Get the active modules for the kernel
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        // Get the workbench to change the artisan command descriptions
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn("OtherModule");
        // Check if the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // Check if the module is already active
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);
        // Activate the modules if its not active
        $this->moduleManager->shouldReceive("moduleIsActive")->withArgs([$module])->andReturn(true);

        // Get info stating the module is already active
        $response->expectsOutput("The module \"$module\" is already active.");
        $response->run();
    }

    public function testModuleDoesNotExist(): void
    {
        $module = "MyModule";

        // If I activate a module
        $response = $this->artisan("module:activate", ["name" => $module]);

        // Get the active modules for the kernel
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        // Get the workbench to change the artisan command descriptions
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn("OtherModule");
        // Check if the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // Check if the module is already active
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);

        // Get info stating the module doesn't exist
        $response->expectsOutput("There is no module named \"$module\".");
        $response->run();
    }
}
