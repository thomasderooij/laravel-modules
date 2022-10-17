<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetCacheValidityTest extends ModuleManagerTest
{
    private string $method = "getDefaultCacheValidity";

    /**
     * Here we test the default value of the cache validity. We do this because we want to this test to fail when
     *  this value gets changed and breaks backward compatibility.
     */
    public function testGetCacheValidity(): void
    {
        // If I have a method
        $uut = $this->getMethod($this->method);
        // And the config specifies 7 days
        Config::shouldReceive('get')->withArgs(['modules.cache_validity', null])->andReturn($weekInSeconds = 12 * 7 * 24 * 60 * 60);

        // I expect the active modules tracker key to be returned
        $moduleManager = $this->getMockManager($this->method);
        $this->assertSame($weekInSeconds, $uut->invoke($moduleManager));
    }
}
