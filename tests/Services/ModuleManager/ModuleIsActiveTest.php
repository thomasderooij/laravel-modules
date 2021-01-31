<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

class ModuleIsActiveTest extends ModuleManagerTest
{
    private $method = "moduleIsActive";

    public function testModuleIsActive () : void
    {
        $uut = $this->getMockManager($this->method);

        // If I have a module
        $module = "aCtIvE_mOdUlE";

        // I fetch the active modules
        $modules = ["module_4", "active_module"];
        $uut->shouldReceive("getActiveModules")->andReturn($modules);

        // And sanitise my module name
        $uut->shouldReceive("sanitiseModuleName")->withArgs(["module_4"])->andReturn("module_4");
        $uut->shouldReceive("sanitiseModuleName")->withArgs(["active_module"])->andReturn("active_module");
        $uut->shouldReceive("sanitiseModuleName")->withArgs(["aCtIvE_mOdUlE"])->andReturn("active_module");

        // And expect my module to be marked as active
        $this->assertTrue($uut->moduleIsActive($module));
    }

    public function testModuleIsNotActive () : void
    {
        $uut = $this->getMockManager($this->method);

        // If I have a module
        $module = "aCtIvE_mOdUlE";

        // I fetch the active modules
        $modules = ["module_4", "inactive_module"];
        $uut->shouldReceive("getActiveModules")->andReturn($modules);

        // And sanitise my module name
        $uut->shouldReceive("sanitiseModuleName")->withArgs(["module_4"])->andReturn("module_4");
        $uut->shouldReceive("sanitiseModuleName")->withArgs(["inactive_module"])->andReturn("inactive_module");
        $uut->shouldReceive("sanitiseModuleName")->withArgs(["aCtIvE_mOdUlE"])->andReturn("active_module");

        // And expect my module to not be marked as active
        $this->assertFalse($uut->moduleIsActive($module));
    }
}
