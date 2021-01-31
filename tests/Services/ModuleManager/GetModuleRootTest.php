<?php


namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Support\Facades\Config;

class GetModuleRootTest extends ModuleManagerTest
{
    private $method = "getModuleRoot";

    public function testGetModuleRoot () : void
    {
        // If I want to get my modules root dir
        $uut = $this->getMockManager($this->method);

        // The cache should fetch the module root
        $root = "modulesRoot";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root);

        // Then we get the sanitised module name
        $module = "TestModule";
        $uut->shouldReceive("sanitiseModuleName")->withArgs([$module])->andReturn($module);

        // And I expect the modules root to me the following
        $expected = "$root/$module";
        $this->assertSame($expected, $uut->getModuleRoot($module));
    }
}
