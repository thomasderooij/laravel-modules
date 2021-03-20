<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

use Thomasderooij\LaravelModules\Services\ModuleStateRepository;

class GetJsonOptionsTest extends ModuleStateRepositoryTest
{
    protected $method = "getJsonOptions";

    public function testGetJsonOptions () : void
    {
        // I expect the following json options when I ask for them
        $expected = [JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES];

        $uut = $this->getMethodFromClass($this->method, ModuleStateRepository::class);
        $mockRepo = $this->getMockRepository($this->method);

        $this->assertSame(array_sum($expected), array_sum($uut->invoke($mockRepo, $this->method)));
    }
}
