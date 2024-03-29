<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetCacheKeyTest extends ModuleManagerTest
{
    private $method = "getCacheKey";

    /**
     * Here we test the default value of the cache key. We do this because we want to this test to fail when
     *  this value gets changed and breaks backward compatibility.
     */
    public function testGetCacheKey(): void
    {
        // If I have a method
        $uut = $this->getMethod($this->method);

        // I expect the active modules tracker key to be returned
        $moduleManager = $this->getMockManager($this->method);
        $expected = "modules-cache";
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }
}
