<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

class GetTrackerFileNameTest extends ModuleStateRepositoryTest
{
    private $method = "getTrackerFileName";

    public function testGetTrackerFile(): void
    {
        $uut = $this->getMockRepository($this->method);

        // I expect the tracker file name
        $expected = ".tracker";

        // When I ask for the tracker file name
        $this->assertSame($expected, $uut->getTrackerFileName());
    }
}
