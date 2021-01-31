<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;

class GetModulesDirectoryTest extends ModuleManagerTest
{
    private $method = "getModulesDirectory";

    public function testGetModulesDirectory () : void
    {
        $uut = $this->getMockManager($this->method);

        // I should check if there is a config file
        $uut->shouldReceive("hasConfig")->andReturn(true);

        // I should fetch the modules root from the config file
        $modulesRoot = "test_modules";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($modulesRoot);

        // And I should get the directory
        $expected = base_path($modulesRoot);

        // When I ask for the modules directory
        $this->assertSame($expected, $uut->getModulesDirectory());
    }

    public function testGetModulesDirectoryWithoutConfig () : void
    {
        $uut = $this->getMockManager($this->method);

        // If I don't have a config file
        $uut->shouldReceive("hasConfig")->andReturn(false);

        // I expect to get an exception
        $this->expectException(ConfigFileNotFoundException::class);
        // With a message
        $this->expectExceptionMessage("Could not locate modules file in the config directory.");

        // When I ask for the modules directory
        $uut->getModulesDirectory();
    }
}
