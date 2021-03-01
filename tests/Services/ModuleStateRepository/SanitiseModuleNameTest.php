<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

class SanitiseModuleNameTest extends ModuleStateRepositoryTest
{
    private $method = "sanitiseModuleName";

    /**
     * Here, we get the module name out of the tracker file, exactly as it was put in originally
     */
    public function testSanitiseModuleName () : void
    {
        $moduleManager = $this->getMockRepository($this->method);
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
