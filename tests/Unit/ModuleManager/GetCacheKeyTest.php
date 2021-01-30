<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetCacheKeyTest extends ModuleManagerTest
{
    /**
     * Here we test the default value of the cache key. We do this because we want to this test to fail when
     *  this value gets changed and breaks backward compatibility.
     */
    public function testGetCacheKey () : void
    {
        // If I have a method
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod("getCacheKey");
        $uut->setAccessible(true);

        // I expect the active modules tracker key to be returned
        $moduleManager = $this->app->make("module.service.manager");
        $expected = "modules-cache";
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }
}
