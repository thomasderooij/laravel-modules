<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Thomasderooij\LaravelModules\Exceptions\ModuleAlreadyActiveException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class ActivateModuleTest extends ModuleManagerTest
{
    private $method = "activateModule";

    /**
     * All public methods called by the module will be mocked, since they will also get their own test
     *  As a result, we will not be testing them in this test.
     */
    public function testActivateModule () : void
    {
        $uut = $this->getMockManager($this->method);

        // If I want to activate a module
        $module = "test_module";

        // We the module should already exist
        $uut->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);
        // And not yet be active
        $uut->shouldReceive("moduleIsActive")->withArgs([$module])->andReturn(false);
        // Next we will need the tracker file content
        $modulesKey = "modules_key";
        $activeModulesKey = "active_modules_key";
        $trackerContent = [$modulesKey => [$module], $activeModulesKey => []];
        $uut->shouldReceive("getTrackerContent")->withNoArgs()->andReturn($trackerContent);
        // Next we fetch the active modules key
        $uut->shouldReceive("getActiveModulesTrackerKey")->withNoArgs()->andReturn($activeModulesKey);
        // Next we sanitise the module name
        $uut->shouldReceive("sanitiseModuleName")->withArgs([$module])->andReturn($module);

        // And we should save an updated tracker file, indicating the module is now active
        $updatedTrackerContent = [
            $modulesKey => [$module],
            $activeModulesKey => [$module]
        ];
        // And save that to the current tracker file
        $uut->shouldReceive("save")->withArgs([$updatedTrackerContent]);

        $uut->activateModule($module);
    }

    public function testThrowingExceptionIfThereIsNoSuchModule () : void
    {
        $uut = $this->getMockManager($this->method);

        // If I have a module name
        $module = "non_existent_module";

        // But that module does not actually exist
        $uut->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);

        // I should get an exception
        $this->expectException(ModuleNotFoundException::class);
        // With a message
        $this->expectExceptionMessage("There is no module named \"$module\".");

        // When I try to activate it
        $uut->activateModule($module);
    }

    public function testThrowingExceptionIfTheModuleIsAlreadyActive () : void
    {
        $uut = $this->getMockManager($this->method);

        // If I have a module
        $module = "active_module";
        $uut->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);

        // And the module is already active
        $uut->shouldReceive("moduleIsActive")->withArgs([$module])->andReturn(true);

        // I should get an exception
        $this->expectException(ModuleAlreadyActiveException::class);
        // With a message
        $this->expectExceptionMessage("The module \"$module\" is already active.");

        // When I try to activate that module
        $uut->activateModule($module);
    }
}
