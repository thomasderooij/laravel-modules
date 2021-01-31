<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetWorkbenchKeyTest extends ModuleManagerTest
{
    private $method = "getWorkbenchKey";

    public function testGetWorkbenchKey () : void
    {
        // If I have a function to get a workbench key
        $uut = $this->getMethod($this->method);

        $moduleManager = $this->getMockManager($this->method);

        // I expect the workbench key
        $expected = "workbench";

        // When I ask for the workbench key
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }
}
