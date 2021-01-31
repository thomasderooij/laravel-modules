<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories\ServiceProviderFactory;

use Thomasderooij\LaravelModules\Tests\Factories\ServiceProviderFactoryTest;

class GetProvidersRootTest extends ServiceProviderFactoryTest
{
    private $method = "getProvidersRoot";

    public function testGetProvidersDirectory () : void
    {
        $uut = $this->getMethod($this->method);
        $factory = $this->getMockServiceProviderFactory($this->method);

        $expected = "Providers";
        $this->assertSame($expected, $uut->invoke($factory));
    }
}
