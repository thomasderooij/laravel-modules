<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Illuminate\Support\Facades\Cache;

class ClearWorkbenchTest extends ModuleManagerTest
{
    public function testUnset () : void
    {
        $uut = $this->getMockManager(null, [
            "getCacheKey",
            "getCacheValidity",
            "getWorkbenchKey",
            "throwExceptionIfNotInitialised",
        ]);

        // We should check if the modules are initialised before moving further
        $uut->shouldReceive("throwExceptionIfNotInitialised")->once();
        // Then we fetch the cache key
        $cacheKey = 'cache_key';
        $uut->shouldReceive('getCacheKey')->andReturn($cacheKey);
        // And we fetch the workbench key
        $workbenchKey = 'workbench_key';
        $uut->shouldReceive('getWorkbenchKey')->andReturn($workbenchKey);
        // And we fetch the validity
        $cacheValidity = 1000;
        $uut->shouldReceive('getCacheValidity')->andReturn($cacheValidity);

        // We then fetch the modules cache
        $preexistingCache = ["somekey" => "somevalue", $workbenchKey => "module_name"];
        Cache::shouldReceive('get')->withArgs([$cacheKey])->andReturn($preexistingCache);
        // And place an empties workbench key inside it
        Cache::shouldReceive('put')->withArgs([$cacheKey, ["somekey" => "somevalue", $workbenchKey => null], $cacheValidity])->once();

        $uut->clearWorkbench();
    }
}
