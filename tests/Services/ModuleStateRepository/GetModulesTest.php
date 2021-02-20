<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetModulesTest extends ModuleStateRepositoryTest
{
    private $method = "getModules";

    public function testGetModules () : void
    {
        // If I have a method to get all the modules
        $uut = $this->getMethod($this->method);

        $moduleManager = $this->getMockRepository($this->method);

        // I should check if the modules are initialised
        $moduleManager->expects("isInitialised")->andReturn(true);

        // Then I should get the tracker content
        $modulesKey = "modules_key";
        $modules = ["module_1", "other_module", "inactive_module"];
        $trackerContent = [$modulesKey => $modules, "active_modules_key" => ["module_1", "other_module"]];
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
        $uut = $this->getMethod($this->method);

        $moduleManager = $this->getMockRepository($this->method);

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
