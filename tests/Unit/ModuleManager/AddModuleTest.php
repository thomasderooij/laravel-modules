<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Exceptions\ModuleCreationException;

class AddModuleTest extends ModuleManagerTest
{
    private $method = "addModule";

    public function testAddingAModule () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If I have a module name
        $module = "new_module";

        // And this module does not exist yet
        $uut->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);

        // Next I should get the tracker content
        $modulesKey = "modules_key";
        $trackerContent = [$modulesKey => [], "active_modules_key" => []];
        $uut->shouldReceive("getTrackerContent")->withNoArgs()->andReturn($trackerContent);

        // Next we'll get the modules tracker key
        $uut->shouldReceive("getModulesTrackerKey")->andReturn($modulesKey)->twice();

        // Then the module should be saved to the module tracker file
        $updatedTrackerContent = [$modulesKey => [$module], "active_modules_key" => []];
        $uut->shouldReceive("save")->withArgs([$updatedTrackerContent])->once();

        // And the module should be activated
        $uut->shouldReceive("activateModule")->withArgs([$module])->once();

        // When I add the module
        $uut->addModule($module);
    }

    public function testAddingAModuleTwice () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If I have a module name
        $module = "new_module";

        // And this module already exists
        $uut->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);

        // I expect an exception
        $this->expectException(ModuleCreationException::class);
        // With a message
        $this->expectExceptionMessage("The module \"$module\" already exists.");

        // When I try to add the module again
        $uut->addModule($module);
    }
}
