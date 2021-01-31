<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories\ServiceProviderFactory;

class GetServiceProviderDirTest extends ServiceProviderFactoryTest
{
    private $method = "getServiceProviderDir";

    public function testGetServiceProviderDir () : void
    {
        // If I have a method to get the service provider directory
        $uut = $this->getMethod($this->method);
        $factory = $this->getMockServiceProviderFactory($this->method);

        // The module manager should fetch the module directory
        $module = "testModule";
        $this->moduleManager->shouldReceive("getModuleDirectory")->withArgs([$module])->andReturn($module);

        // And I should fetch the service provider root name
        $providersRoot = "providers_root";
        $factory->shouldReceive("getProvidersRoot")->andReturn($providersRoot);

        // And I expect the providers directory to be returned
        $expected = "$module/$providersRoot";
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }
}
