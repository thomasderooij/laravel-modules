<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories\ServiceProviderFactory;

use Thomasderooij\LaravelModules\Tests\Factories\ServiceProviderFactoryTest;

class GetModuleRoutesTest extends ServiceProviderFactoryTest
{
    private $method = "getModuleRoutesRoot";

    public function testGetModuleRoutesRoot(): void
    {
        // In order to get the relative module directory
        $uut = $this->getMethod($this->method);
        $factory = $this->getMockServiceProviderFactory($this->method);

        // I should get the module directory
        $root = "modules/TestModule";
        $module = "testModule";
        $this->moduleManager->shouldReceive("getModuleRoot")->withArgs([$module])->andReturn($root);

        // And I should get the name of the routes dir
        $routes = "routes_dir";
        $this->routeSource->shouldReceive("getRouteRootDir")->andReturn($routes);

        // And I should expect the relative path the routes directory
        $expected = "$root/$routes";
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }
}
