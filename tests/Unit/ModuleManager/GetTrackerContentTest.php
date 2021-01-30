<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetTrackerContentTest extends ModuleManagerTest
{
    private $method = "getTrackerContent";

    public function testGetTrackerContent () : void
    {
        // If I have a method to ask for the modules tracker key
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);

        $moduleManager = $this->getMockManager(null, $this->method);
    }
}
