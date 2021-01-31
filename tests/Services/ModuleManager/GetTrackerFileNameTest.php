<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

class GetTrackerFileNameTest extends ModuleManagerTest
{
    private $method = "getTrackerFileName";

    public function testGetTrackerFile () : void
    {
        $uut = $this->getMockManager($this->method);

        // I expect the tracker file name
        $expected = ".tracker";

        // When I ask for the tracker file name
        $this->assertSame($expected, $uut->getTrackerFileName());
    }
}
