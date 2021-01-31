<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetModulesRootTest extends ModuleManagerTest
{
    private $method = "getModulesRoot";

    public function testGetModulesRoot () : void
    {
        // If I have a method to ask for the modules root
        $uut = $this->getMethod($this->method);

        $moduleManager = $this->getMockManager($this->method);

        // I should check if I have a configuration file
        $moduleManager->expects("hasConfig")->andReturn(true);

        // And I should fetch the module root from the configuration
        $root = "modules_root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root);

        // Which is exactly what I'd expect when I ask this from the module manager
        $expected = $root;
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }

    public function testGetModulesRootWithoutAConfigFile () : void
    {
        // If I have a method to ask for the modules root
        $uut = $this->getMethod($this->method);

        $moduleManager = $this->getMockManager($this->method);

        // And I don't have a config file
        $moduleManager->expects("hasConfig")->andReturn(false);

        // I expect an exception
        $this->expectException(ConfigFileNotFoundException::class);
        // With a message
        $this->expectExceptionMessage("Could not locate modules file in the config directory.");

        // When I call the function
        $uut->invoke($moduleManager);
    }
}
