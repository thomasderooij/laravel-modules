<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories\ServiceProviderFactory;

class GetFileNameTest extends ServiceProviderFactoryTest
{
    private $method = "getFileName";

    public function testGetFileName () : void
    {
        $uut = $this->getMethod($this->method);
        $factory = $this->getMockServiceProviderFactory($this->method);

        // If get a class name
        $className = "TestServiceProvider";
        $factory->shouldReceive("getClassName")->andReturn($className);

        // Then I expect the filename to be returned
        $expected = "$className.php";
        $this->assertSame($expected, $uut->invoke($factory));
    }
}
