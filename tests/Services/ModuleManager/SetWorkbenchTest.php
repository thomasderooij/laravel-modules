<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Support\Facades\Cache;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class SetWorkbenchTest extends ModuleManagerTest
{
    private $method = "setWorkbench";

    /**
     * Test setting a workbench if all goes well
     */
    public function testSetWorkbenchWithoutACurrentWorkbench () : void
    {
        // If I have a module manager on which I want to set a workbench
        $uut = $this->getMockManager(null, $this->method);

        // It should check if it should throw an exception for not having been initialised
        $uut->shouldReceive("isInitialised")->andReturn(true)->once();

        // And it check if the module exists
        $moduleName = "TeSt_MoDuLe";
        $uut->shouldReceive("hasModule")->withArgs([$moduleName])->andReturn(true)->once();

        // It should then sanitise the module name, meaning it should ignore all capitalisation, and return standardised module name
        $uut->shouldReceive("sanitiseModuleName")->withArgs([$moduleName])->andReturn("test_module")->once();

        // The workbench key should be fetched to read and write to the bench
        $workbenchKey = "workbench_key";
        $uut->shouldReceive("getWorkbenchKey")->andReturn($workbenchKey); // the actual key doesn't matter in this test

        // The cache key should be provided to store a new module in the workbench
        $actualKey = "module_key";
        $uut->shouldReceive('getCacheKey')->andReturn($actualKey);

        // And the timeframe of the cache validity should be provided
        $cacheValitidy = 3600;
        $uut->shouldReceive('getCacheValidity')->andReturn($cacheValitidy);

        // The cache should try to get a cache key, wopwop, from above, and return null
        Cache::shouldReceive('get')->withArgs([$actualKey])->andReturn(null)->once();

        // And in the end, the cache should store the new module under then new cache key
        Cache::shouldReceive('put')->withArgs([$actualKey, [$workbenchKey => strtolower($moduleName)], $cacheValitidy]);

        $uut->setWorkbench($moduleName);
    }

    /**
     * Here we test setting a different module to the workbench
     */
    public function testSetWorkbenchWithACurrentWorkbench () : void
    {
        // If I have a module manager on which I want to set a workbench
        $uut = $this->getMockManager(null, $this->method);

        // It should check if it should throw an exception for not having been initialised
        $uut->shouldReceive("isInitialised")->andReturn(true)->once();

        // And it check if the module exists
        $moduleName = "TeSt_MoDuLe";
        $uut->shouldReceive("hasModule")->withArgs([$moduleName])->andReturn(true)->once();

        // It should then sanitise the module name, meaning it should ignore all capitalisation, and return standardised module name
        $uut->shouldReceive("sanitiseModuleName")->withArgs([$moduleName])->andReturn("test_module")->once();

        // The workbench key should be fetched to read and write to the bench
        $workbenchKey = "workbench_key";
        $uut->shouldReceive("getWorkbenchKey")->andReturn($workbenchKey); // the actual key doesn't matter in this test

        // The cache key should be provided to store a new module in the workbench
        $actualKey = "module_key";
        $uut->shouldReceive('getCacheKey')->andReturn($actualKey);

        // And the timeframe of the cache validity should be provided
        $cacheValitidy = 3600;
        $uut->shouldReceive('getCacheValidity')->andReturn($cacheValitidy);

        // The cache should try to get a cache key from above, and return null
        $preexitingCache = ["somekey" => "somevalue", $workbenchKey => "other_module"];
        Cache::shouldReceive('get')->withArgs([$actualKey])->andReturn($preexitingCache)->once();

        // And in the end, the cache should store the new module under then new cache key
        Cache::shouldReceive('put')->withArgs([$actualKey, ["somekey" => "somevalue", $workbenchKey => strtolower($moduleName)], $cacheValitidy]);

        $uut->setWorkbench($moduleName);
    }

    public function testShouldThrowAnExceptionIfNotInitialised () : void
    {
        // If I have a module manager on which I want to set a workbench
        $uut = $this->getMockManager(null, $this->method);

        // And the modules are not initialised
        $uut->shouldReceive('isInitialised')->andReturn(false);

        // I should get an exception
        $this->expectException(ModulesNotInitialisedException::class);

        $uut->setWorkbench("test_module");
    }

    public function testShouldThrowAnExceptionIfModuleDoesNotExist () : void
    {
        // If I have a module manager on which I want to set a workbench
        $uut = $this->getMockManager(null, $this->method);

        // And the modules are initialised
        $uut->shouldReceive('isInitialised')->andReturn(true);

        // But the module does not exist
        $module = "test_module";
        $uut->shouldReceive("hasModule")->withArgs([$module])->andReturn(false);

        // I should get an exception
        $this->expectException(ModuleNotFoundException::class);

        $uut->setWorkbench($module);
    }
}
