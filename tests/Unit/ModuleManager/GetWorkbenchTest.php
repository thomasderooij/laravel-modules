<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Illuminate\Support\Facades\Cache;
use Mockery;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as ModuleManagerContract;

class GetWorkbenchTest extends ModuleManagerTest
{
    private $method = "getWorkbench";

    /**
     * Here we check if we get null when we're asking what's in an empty workbench
     */
    public function testGetWorkbenchWhenEmpty () : void
    {
        // If I have a module manager
        $uut = $this->getMockManager(null, $this->method);

        // We should get the cache key
        $cacheKey = "cache_key";
        $uut->shouldReceive("getCacheKey")->andReturn($cacheKey);

        Cache::shouldReceive('get')->withArgs([$cacheKey])->andReturn(null)->once(); // We don't care about the arguments here

        // And I ask for the workbench
        // I expect to receive null
        $this->assertNull($uut->getWorkbench());
    }

    /**
     * Here we check if we get the module from the workbench
     */
    public function testGetWorkbenchWhenNotEmpty () : void
    {
        // If I have a module manager
        $uut = $this->getMockManager(null, $this->method);

        // We should get the cache key
        $cacheKey = "cache_key";
        $uut->shouldReceive("getCacheKey")->andReturn($cacheKey);

        // And there is an active module
        Cache::shouldReceive('get')->withArgs([$cacheKey])->andReturn(["module" => "testModule"])->once(); // We also don't care about the arguments here
        // The manager should know they key
        $uut->shouldReceive("getWorkbenchKey")->andReturn("module")->once();

        // And I ask for the workbench
        // I expect to receive testModule
        $this->assertSame("testModule", $uut->getWorkbench());
    }
}
