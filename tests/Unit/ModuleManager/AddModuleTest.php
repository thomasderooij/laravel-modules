<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Exceptions\ModuleCreationException;

class AddModuleTest extends ModuleManagerTest
{
    public function testAddingAModule () : void
    {
        $uut = $this->getMockManager(null, [
            "activateModule",
            "getTrackerContent",
            "hasModule",
            "save",
        ]);

        // If I have a module name
        $module = "new_module";

        // And this module does not exist yet
        $uut->expects("hasModule")->withArgs([$module])->andReturn(false);

        // Next I should get the tracker content
        $modulesKey = "modules";
        $trackerContent = [$modulesKey => [], "activeModules" => []];
        $uut->expects("getTrackerContent")->withNoArgs()->andReturn($trackerContent);

        // Then the module should be saved to the module tracker file
        $updatedTrackerContent = [$modulesKey => [$module], "activeModules" => []];
        $uut->expects("save")->withArgs([$updatedTrackerContent])->once();

        // And the module should be activated
        $uut->shouldReceive("activateModule")->withArgs([$module])->once();

        // When I add the module
        $uut->addModule($module);
    }

    public function testAddingAModuleTwice () : void
    {
        $uut = $this->getMockManager(null, ["hasModule"]);

        // If I have a module name
        $module = "new_module";

        // And this module already exists
        $uut->expects("hasModule")->withArgs([$module])->andReturn(true);

        // I expect an exception
        $this->expectException(ModuleCreationException::class);
        // With a message
        $this->expectExceptionMessage("The module \"$module\" already exists.");

        // When I try to add the module again
        $uut->addModule($module);
    }
}
