<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Support\Facades\Cache;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;

class ClearWorkbenchTest extends ModuleManagerTest
{
    private $method = "clearWorkbench";

    /**
     * Test clearing the workbench
     */
    public function testClearingTheWorkbench () : void
    {
        $uut = $this->getMockManager($this->method);

        // We should check if the modules are initialised before moving further
        $uut->shouldReceive("isInitialised")->andReturn(true)->once();
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

    /**
     * Test that an exception gets thrown when the modules are not initialised
     */
    public function testClearingTheWorkbenchWhenModulesAreNotInitialised () : void
    {
        $uut = $this->getMockManager($this->method);

        // When my modules are not initialised
        $uut->shouldReceive("isInitialised")->andReturn(false);

        // I expect an exception
        $this->expectException(ModulesNotInitialisedException::class);
        // With a message
        $this->expectExceptionMessage("The modules need to be initialised first. You can do this by running the module:init command.");

        // When I try to clear the workbench
        $uut->clearWorkbench();
    }
}
