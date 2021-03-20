<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Services\ModuleStateRepository;

class HasConfigTest extends ModuleStateRepositoryTest
{
    protected $method = "hasConfig";

    public function testHasConfig () : void
    {
        $uut = $this->getMethodFromClass($this->method, ModuleStateRepository::class);
        $mockRepo = $this->getMockRepository($this->method);

        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn("root");

        $this->assertTrue($uut->invoke($mockRepo));
    }

    public function testDoesNotHaveConfig () : void
    {
        $uut = $this->getMethodFromClass($this->method, ModuleStateRepository::class);
        $mockRepo = $this->getMockRepository($this->method);

        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn(null);

        $this->assertFalse($uut->invoke($mockRepo));
    }
}
