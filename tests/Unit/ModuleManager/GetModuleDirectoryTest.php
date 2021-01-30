<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

class GetModuleDirectoryTest extends ModuleManagerTest
{
    /**
     * @todo: This function needs to be updated so make sure the directory has the proper capitalisation
     */
    public function testGetModuleDirectory () : void
    {
        $uut = $this->getMockManager(null, ["getModulesDirectory"]);

        // If I have a module
        $module = "test_module";

        // I should get the module root
        $moduleRoot = base_path("module_root");
        $uut->expects("getModulesDirectory")->andReturn($moduleRoot);

        // I should get a relative module directory
        $expected = "$moduleRoot/$module";
        $this->assertSame($expected, $uut->getModuleDirectory($module));
    }
}
