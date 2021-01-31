<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class RemoveModuleTest extends ModuleManagerTest
{
    private $method = "removeModule";

    public function testRemoveModule () : void
    {
        $uut = $this->getMockManager($this->method);

        // To remove a module, the module needs to exist
        $module = "test_module";
        $uut->expects('hasModule')->withArgs([$module])->andReturn(true);
        // The module needs to be deactivated
        $uut->expects("deactivateModule");
        // We sbould check if its in the workbench
        $uut->expects("getWorkbench")->andReturn($module);
        // And clear the workbench
        $uut->expects("clearWorkbench");
        // We sanitise the module name
        $uut->expects("sanitiseModuleName")->withArgs([$module])->twice()->andReturn($module);
        // Get the tracker content
        $modulesKey = "modules_key";
        $trackerContent = [$modulesKey => [$module]];
        $uut->expects("getTrackerContent")->andReturn($trackerContent);
        // And the modules tracker key
        $uut->expects("getModulesTrackerKey")->twice()->andReturn($modulesKey);
        // Then update the tracker content
        $uut->expects("save")->withArgs([[$modulesKey => []]]);
        // Next we fetch the module directory
        $directory = "directory";
        $uut->expects("getModuleDirectory")->withArgs([$module])->andReturn($directory);
        // And then delete that directory
        $this->filesystem->expects("delete")->withArgs([$directory]);

        $uut->removeModule($module);
    }

    public function testRemoveNonExistingModule () : void
    {
        $uut = $this->getMockManager($this->method);

        // To remove a module, the module needs to exist
        $module = "test_module";
        $uut->expects('hasModule')->withArgs([$module])->andReturn(false);

        // And I expect an exception if it does not exist
        $this->expectException(ModuleNotFoundException::class);
        // With some explanation
        $this->expectExceptionMessage("There is no module named \"$module\".");

        $uut->removeModule($module);
    }
}
