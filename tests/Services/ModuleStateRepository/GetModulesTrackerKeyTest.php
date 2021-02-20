<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

class GetModulesTrackerKeyTest extends ModuleStateRepositoryTest
{
    private $method = "getModulesTrackerKey";

    public function testGetModulesTrackerKey () : void
    {
        // If I have a method to ask for the modules tracker key
        $uut = $this->getMethod($this->method);

        // I should receive the tracker key
        $expected = "modules";

        // When I ask the module manager for it
        $modulesManager = $this->getMockRepository($this->method);
        $this->assertSame($expected, $uut->invoke($modulesManager));
    }
}
