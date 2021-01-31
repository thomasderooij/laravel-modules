<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

class HasModuleTest extends ModuleManagerTest
{
    private $method = "hasModule";

    public function testHasModule () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If I have modules
        $modules = ["module_1", "other_module", "inactive_module"];

        // And I am looking for a module
        $module = "module_1";

        // I sanitise the module name
        $uut->expects("sanitiseModuleName")->withArgs([$module])->twice()->andReturn($module);
        $uut->expects("sanitiseModuleName")->withArgs(["other_module"])->andReturn("other_module");
        $uut->expects("sanitiseModuleName")->withArgs(["inactive_module"])->andReturn("inactive_module");

        // And I ask for the modules
        $uut->expects("getModules")->andReturn($modules);

        // I expect to get a confirmation
        $this->assertTrue($uut->hasModule($module));
    }

    public function testHasModuleWhenYouDoNotHaveTheModule () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If I have modules
        $modules = ["module_2", "other_module", "inactive_module"];

        // And I am looking for a module
        $module = "module_1";

        // I sanitise the module name
        $uut->expects("sanitiseModuleName")->withArgs([$module])->andReturn($module);
        $uut->expects("sanitiseModuleName")->withArgs(["module_2"])->andReturn("module_2");
        $uut->expects("sanitiseModuleName")->withArgs(["other_module"])->andReturn("other_module");
        $uut->expects("sanitiseModuleName")->withArgs(["inactive_module"])->andReturn("inactive_module");

        // And I ask for the modules
        $uut->expects("getModules")->andReturn($modules);

        // I expect to get a false
        $this->assertFalse($uut->hasModule($module));
    }
}
