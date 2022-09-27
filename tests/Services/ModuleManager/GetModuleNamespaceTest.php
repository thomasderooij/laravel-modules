<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;

class GetModuleNamespaceTest extends ModuleManagerTest
{
    private $method = "getModuleNamespace";

    public function testGetModuleNamespace(): void
    {
        $uut = $this->getMockManager($this->method);

        // If I have a module
        $module = "test_module";

        // We should check if there is a configuration file
        $uut->shouldReceive("hasConfig")->andReturn(true);
        // And we should get the sanitised module name
        $uut->shouldReceive("sanitiseModuleName")->andReturn($module);

        // I should get the module root from the configuration
        $moduleRoot = "module_root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn("Module_root");

        // I should receive the module namespace
        $expected = "Module_root\\Test_module\\";

        // When I call the function
        $this->assertSame($expected, $uut->getModuleNamespace($module));
    }

    public function testGetModuleNamespaceWithoutConfigFiles(): void
    {
        $uut = $this->getMockManager($this->method);

        // If I have a module
        $module = "test_module";

        // If there is no configuration file
        $uut->shouldReceive("hasConfig")->andReturn(false);

        // I expect to receive an exception
        $this->expectException(ConfigFileNotFoundException::class);
        // With a message
        $this->expectExceptionMessage("Could not locate modules file in the config directory.");

        // When I ask for the module namespace
        $uut->getModuleNamespace($module);
    }
}
