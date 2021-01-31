<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetWorkbenchKeyTest extends ModuleManagerTest
{
    private $method = "getWorkbenchKey";

    public function testGetWorkbenchKey () : void
    {
        // If I have a function to get a workbench key
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);

        $moduleManager = $this->getMockManager(null, $this->method);

        // I expect the workbench key
        $expected = "workbench";

        // When I ask for the workbench key
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }
}
