<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetModuleNamespaceTest extends ModuleManagerTest
{
    public function testGetModuleNamespace () : void
    {
        $uut = $this->getMockManager(null, ["hasConfig"]);

        // If I have a module
        $module = "test_module";

        // We should check if there is a configuration file
        $uut->expects("hasConfig")->andReturn(true);

        // I should get the module root from the configuration
        $moduleRoot = "module_root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn("Module_root");

        // I should receive the module namespace
        $expected = "Module_root\\Test_module\\";

        // When I call the function
        $this->assertSame($expected, $uut->getModuleNameSpace($module));
    }

    public function testGetModuleNamespaceWithoutConfigFiles () : void
    {
        $uut = $this->getMockManager(null, ["hasConfig"]);

        // If I have a module
        $module = "test_module";

        // If there is no configuration file
        $uut->expects("hasConfig")->andReturn(false);

        // I expect to receive an exception
        $this->expectException(ConfigFileNotFoundException::class);
        // With a message
        $this->expectExceptionMessage("Could not locate modules file in the config directory.");

        // When I ask for the module namespace
        $uut->getModuleNameSpace($module);
    }
}
