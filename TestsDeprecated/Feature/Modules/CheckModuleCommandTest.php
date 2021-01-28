<?php

namespace TestsDeprecated\Feature\Modules;

class CheckModuleCommandTest extends ModuleTest
{
    public function testCheckingModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $this->createModule();

        // And check the module in workbench
        $response = $this->artisan("module:check");

        // I should get the name of my module back. In lowercase
        $response->expectsOutput($this->module);
    }

    public function testCheckingModuleIfWorkbenchIsEmpty () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $this->createModule();

        // And my workbench is empty
        $this->moduleManager->clearWorkbench();

        // And check the module in the workbench
        $response = $this->artisan("module:check");

        // I should get the name of my module back. In lowercase
        $response->expectsOutput("Your workbench is empty.");
    }
}
