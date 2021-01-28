<?php

namespace TestsDeprecated\Feature\Modules;

class SetModuleCommandTest extends ModuleTest
{
    public function testSettingModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And the workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I set the module to the workbench
        $response = $this->artisan("module:set", ["name" => $module]);
        $response->expectsOutput("The module $module is now set to your workbench.");
        $response->run();

        // My workbench should contain my module
        $this->assertSame($module, $this->moduleManager->getWorkBench());
    }

    public function testSettingANonExistentModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And the workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I set a non existent module to the workbench
        $response = $this->artisan("module:set", ["name" => "wrongModule"]);
        $response->expectsOutput("There is no module named wrongModule.");
        $response->run();

        // My workbench should be empty
        $this->assertNull($this->moduleManager->getWorkBench());
    }
}
