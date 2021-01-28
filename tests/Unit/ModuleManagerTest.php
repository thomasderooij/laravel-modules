<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as ModuleManagerContract;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class ModuleManagerTest extends Test
{
    /**
     * Here we check if we get null when we're asking what's in an empty workbench
     */
    public function testGetWorkbenchWhenEmpty () : void
    {
        $this->getMockManager($this->getMockFilesystem(), []);

        // If I have a module manager
        /** @var ModuleManagerContract $uut */
        $uut = $this->app->make('module.service.manager');

        Cache::shouldReceive('get')->andReturn(null)->once(); // We don't care about the arguments here

        // And I ask for the workbench
        // I expect to receive null
        $this->assertNull($uut->getWorkbench());
    }

    /**
     * Here we check if we get the module from the workbench
     */
    public function testGetWorkbenchWhenNotEmpty () : void
    {
        $uut = $this->getMockManager(null, ["getWorkbenchKey"]);

        // If I have a module manager
        /** @var ModuleManagerContract&Mockery\MockInterface $uut */
        $uut = $this->getMockManager(null, ["getWorkbenchKey"]);

        // And there is an active module
        Cache::shouldReceive('get')->andReturn(["module" => "testModule"])->once(); // We also don't care about the arguments here
        // The manager should know they key
        $uut->shouldReceive("getWorkbenchKey")->andReturn("module")->once();

        // And I ask for the workbench
        // I expect to receive testModule
        $this->assertSame("testModule", $uut->getWorkbench());
    }

    private function getMockFilesystem () : Mockery\MockInterface
    {
        $filesystem = Mockery::mock(Filesystem::class);
        $this->instance('files', $filesystem);

        return $filesystem;
    }

    private function getMockManager (Filesystem $filesystem = null, array $mockFunctions = []) : Mockery\MockInterface
    {
        if ($filesystem === null) {
            $filesystem = $this->getMockFilesystem();
        }

        $functions = implode(", ", $mockFunctions);
        $mock = Mockery::mock(ModuleManager::class."[$functions]", [$filesystem]);
        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }
}
