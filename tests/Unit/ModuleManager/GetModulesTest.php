<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetModulesTest extends ModuleManagerTest
{
    public function testGetModules () : void
    {
        // If I have a method to get all the modules
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod("getModules");
        $uut->setAccessible(true);

        $moduleManager = $this->getMockManager(null, [
            "getModulesTrackerKey",
            "getTrackerContent",
            "isInitialised"
        ]);

        // I should check if the modules are initialised
        $moduleManager->expects("isInitialised")->andReturn(true);

        // Then I should get the tracker content
        $modulesKey = "modules";
        $modules = ["module_1", "other_module", "inactive_module"];
        $trackerContent = [$modulesKey => $modules, "activeModules" => ["module_1", "other_module"]];
        $moduleManager->expects("getTrackerContent")->andReturn($trackerContent);

        // Then I should get the modules key
        $moduleManager->expects("getModulesTrackerKey")->andReturn($modulesKey);

        // I expect to get my modules
        $expected = $modules;
        // When I call the function
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }

    public function testGetModulesIfModulesAreNotInitialised () : void
    {
        // If I have a method to get all the modules
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod("getModules");
        $uut->setAccessible(true);

        $moduleManager = $this->getMockManager(null, ["isInitialised"]);

        // And the modules are not initialised
        $moduleManager->expects("isInitialised")->andReturn(false);

        // I expect an exception
        $this->expectException(ModulesNotInitialisedException::class);
        // With a message
        $this->expectExceptionMessage("The modules need to be initialised first. You can do this by running the module:init command.");

        // When I call the function
        $uut->invoke($moduleManager);
    }
}
