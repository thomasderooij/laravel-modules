<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotActiveException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class DeactivateModuleTest extends ModuleManagerTest
{
    private $method = "deactivateModule";

    public function testDeactivatingAModuleCurrentlyInOurWorkbench () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If my modules are initialised
        $uut->shouldReceive("isInitialised")->once()->andReturn(true);
        // And I have a module
        $module = "aCtIvE_mOdUlE";
        $otherModule = "other_module";
        $uut->shouldReceive("hasModule")->withArgs([$module])->once()->andReturn(true);
        // And the module is active
        $uut->shouldReceive("moduleIsActive")->withArgs([$module])->once()->andReturn(true);
        // Next we should discover the module is active in our workbench
        $sanitisedModuleName = strtolower($module);
        $uut->shouldReceive("sanitiseModuleName")->withArgs([$module])->andReturn($sanitisedModuleName);
        $uut->shouldReceive("sanitiseModuleName")->withArgs([$otherModule])->andReturn($otherModule);
        $uut->shouldReceive("getWorkbench")->andReturn($sanitisedModuleName);
        // And then clear the workbench
        $uut->shouldReceive("clearWorkbench");

        // Next we get the tracker content
        $modulesKey = "modules_key";
        $activeModulesKey = "active_modules_key";
        $trackerContent = [$modulesKey => [$sanitisedModuleName, $otherModule], $activeModulesKey => [$module, $otherModule]];
        $uut->shouldReceive("getTrackerContent")->andReturn($trackerContent);
        $uut->shouldReceive("getActiveModulesTrackerKey")->andReturn($activeModulesKey);

        // And then we should save our updated content to our tracker file
        $updatedTrackerContent = [$modulesKey => [$sanitisedModuleName, $otherModule], $activeModulesKey => [$otherModule]];
        $uut->shouldReceive("save")->withArgs([$updatedTrackerContent]);

        // When I deactivate the module
        $uut->deactivateModule($module);
    }

    public function testDeactivatingAModuleNotInOurWorkbench () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If my modules are initialised
        $uut->shouldReceive("isInitialised")->once()->andReturn(true);
        // And I have a module
        $module = "aCtIvE_mOdUlE";
        $otherModule = "other_module";
        $uut->shouldReceive("hasModule")->withArgs([$module])->once()->andReturn(true);
        // And the module is active
        $uut->shouldReceive("moduleIsActive")->withArgs([$module])->once()->andReturn(true);
        // Next we should discover the module is not active in our workbench
        $sanitisedModuleName = strtolower($module);
        $uut->shouldReceive("sanitiseModuleName")->withArgs([$module])->andReturn($sanitisedModuleName);
        $uut->shouldReceive("sanitiseModuleName")->withArgs([$otherModule])->andReturn($otherModule);
        $uut->shouldReceive("getWorkbench")->andReturn("some_other_module");

        // Next we get the tracker content
        $modulesKey = "modules_key";
        $activeModulesKey = "active_modules_key";
        $trackerContent = [$modulesKey => [$sanitisedModuleName, $otherModule], $activeModulesKey => [$module, $otherModule]];
        $uut->shouldReceive("getTrackerContent")->andReturn($trackerContent);
        $uut->shouldReceive("getActiveModulesTrackerKey")->andReturn($activeModulesKey);

        // And then we should save our updated content to our tracker file
        $updatedTrackerContent = [$modulesKey => [$sanitisedModuleName, $otherModule], $activeModulesKey => [$otherModule]];
        $uut->shouldReceive("save")->withArgs([$updatedTrackerContent]);

        // When I deactivate the module
        $uut->deactivateModule($module);
    }

    public function testDeactivatingAModuleWhenModulesAreNotInitialised () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If my modules are not initialised
        $uut->shouldReceive("isInitialised")->once()->andReturn(false);

        // I expect an exception
        $this->expectException(ModulesNotInitialisedException::class);
        // With a message
        $this->expectExceptionMessage("The modules need to be initialised first. You can do this by running the module:init command.");

        // When I try to deactivate a module
        $uut->deactivateModule("some_module");
    }

    public function testDeactivatingANonExistentModule () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If my modules are initialised
        $module = "non_existent_module";
        $uut->shouldReceive("isInitialised")->once()->andReturn(true);

        // And I don't have the specified module
        $uut->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);

        // I expect an exception
        $this->expectException(ModuleNotFoundException::class);
        // With a message
        $this->expectExceptionMessage("There is no module named \"$module\".");

        // When I try to deactivate a module
        $uut->deactivateModule($module);
    }

    public function testDeactivatingAnAlreadyInactiveModule () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If my modules are initialised
        $module = "inactive_module";
        $uut->shouldReceive("isInitialised")->once()->andReturn(true);

        // And the specified module exists
        $uut->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);

        // But the module is already inactive
        $uut->shouldReceive("moduleIsActive")->withArgs([$module])->andReturn(false);

        // I expect an exception
        $this->expectException(ModuleNotActiveException::class);
        // With a message
        $this->expectExceptionMessage("The module \"$module\" is already inactive.");

        // When I try to deactivate a module
        $uut->deactivateModule($module);
    }
}
