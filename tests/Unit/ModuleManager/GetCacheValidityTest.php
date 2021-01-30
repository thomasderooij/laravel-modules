<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetCacheValidityTest extends ModuleManagerTest
{
    private $method = "getCacheValidity";

    /**
     * Here we test the default value of the cache validity. We do this because we want to this test to fail when
     *  this value gets changed and breaks backward compatibility.
     */
    public function testGetCacheValidity () : void
    {
        // If I have a method
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);

        // I expect the active modules tracker key to be returned
        $moduleManager = $this->getMockManager(null, $this->method);
        $expected = 60 * 60 * 24 * 7;
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }
}
