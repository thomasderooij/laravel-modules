<?php

namespace Tests\Feature\Modules;

class UnsetModuleCommandTest extends ModuleTest
{
    public function testUnsettingModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        $workbench = $this->moduleManager->getWorkBench();

        // And I set the module to the workbench
        $response = $this->artisan("module:unset");
        $response->expectsOutput("Your workbench has been cleared.");
        $response->run();

        // My workbench should contain my module
        $this->assertNull($this->moduleManager->getWorkBench());
        $this->assertNotSame($workbench, $this->moduleManager->getWorkBench());
    }
}
