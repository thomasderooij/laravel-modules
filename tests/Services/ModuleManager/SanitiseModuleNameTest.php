<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class SanitiseModuleNameTest extends ModuleManagerTest
{
    private $method = "sanitiseModuleName";

    /**
     * Here, we get the module name out of the tracker file, exactly as it was put in originally
     */
    public function testSanitiseModuleName () : void
    {
        $moduleManager = $this->getMockManager($this->method);
        $uut = $this->getMethod($this->method);

        // If I have modules
        $module = "TestModule";
        $modules = [$module, "other_module"];
        $moduleManager->shouldReceive("getModules")->andReturn($modules);

        // I should get the same module back, regardless of case
        $expected = $module;
        $this->assertSame($expected, $uut->invoke($moduleManager, strtolower($module)));
    }
}
