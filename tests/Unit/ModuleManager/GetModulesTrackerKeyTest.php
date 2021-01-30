<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetModulesTrackerKeyTest extends ModuleManagerTest
{
    private $method = "getModulesTrackerKey";

    public function testGetModulesTrackerKey () : void
    {
        // If I have a method to ask for the modules tracker key
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);

        // I should receive the tracker key
        $expected = "modules";

        // When I ask the module manager for it
        $modulesManager = $this->getMockManager(null, $this->method);
        $this->assertSame($expected, $uut->invoke($modulesManager));
    }
}
