<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetActiveModulesTrackerKeyTest extends ModuleManagerTest
{
    private $method = "getActiveModulesTrackerKey";

    /**
     * Here we test the default value of the modules tracker key. We do this because we want to this test to fail when
     *  this value gets changed and breaks backward compatibility.
     */
    public function testGetActiveModulesTrackerKey () : void
    {
        // If I have a method
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);

        // I expect the active modules tracker key to be returned
        $moduleManager = $this->getMockManager(null, $this->method);
        $expected = "activeModules";
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }
}
