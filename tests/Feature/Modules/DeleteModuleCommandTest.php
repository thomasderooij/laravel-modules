<?php

namespace Tests\Feature\Modules;

class DeleteModuleCommandTest extends ModuleTest
{
    public function testDeletingAModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And I call the delete module command
        $response = $this->artisan("module:delete", ["name" => $module])->expectsQuestion("This will delete your module $module and all of the code within it. Are you sure you want to do this?", "Yes, I'm sure");
        $response->expectsOutput("Aaaaaand it's gone.");
        $response->run();

        // There should not be a module directory
        $this->assertFalse(is_dir(base_path("{$this->root}/$module")));

        // My workbench should be empty
        $this->assertNull($this->moduleManager->getWorkBench());

        // And it should not be in the tracker file
        $this->assertFalse($this->moduleManager->moduleIsActive($module));
        $this->assertFalse($this->moduleManager->hasModule($module));
    }

    public function testCancellingADeletion () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And I call the delete module command
        $response = $this->artisan("module:delete", ["name" => $module])->expectsQuestion("This will delete your module $module and all of the code within it. Are you sure you want to do this?", "No, I don't want to delete everything");
        $response->expectsOutput("Gotcha. I'll leave your code intact.");
        $response->run();

        // There should still be a module directory
        $this->assertTrue(is_dir(base_path("{$this->root}/$module")));

        // My workbench should be our module name, in lower case
        $this->assertSame($module, $this->moduleManager->getWorkBench());

        // And it should be in the tracker file
        $this->assertTrue($this->moduleManager->hasModule($module));
    }

    public function testCaseShouldNotMatter () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And I refer to the module in a different case
        $module = strtoupper($module);

        // And I call the delete module command
        $response = $this->artisan("module:delete", ["name" => $module])->expectsQuestion("This will delete your module $module and all of the code within it. Are you sure you want to do this?", "Yes, I'm sure");
        $response->expectsOutput("Aaaaaand it's gone.");
    }

    public function testDeletingANonExistentModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And I call the delete module command on a non-existent module
        $response = $this->artisan("module:delete", ["name" => "wrongModule"]);
        $response->expectsOutput("There is no module named wrongModule.");
        $response->run();

        // There should still be a module directory
        $this->assertTrue(is_dir(base_path("{$this->root}/$module")));

        // My workbench should be our module name, in lower case
        $this->assertSame($module, $this->moduleManager->getWorkBench());

        // And it should be in the tracker file
        $this->assertTrue($this->moduleManager->hasModule($module));
    }

    public function testIShouldNotBeAbleToDeleteAModuleIfThePackageIsNotInitiated () : void
    {
        // If modules are not initiated
        // And I create a module names TestModule
        $response = $this->artisan("module:delete", ["name" => "TestModule"]);

        // I should get an error message
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
    }
}
