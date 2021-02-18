<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository\ModuleStateRepositoryTest;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class ModuleManagerTest extends ModuleStateRepositoryTest
{
    /**
     * @return Mockery\MockInterface&ModuleManager
     */
    protected function getMockManager (string $method) : Mockery\MockInterface
    {
        $mockMethods = $this->getMockableClassMethods(ModuleManager::class, $method);

        $functions = implode(", ", $mockMethods);
        // Mock the module manager with all functions except the one we want to test
        $mock = Mockery::mock(ModuleManager::class."[$functions]", [$this->filesystem]);
        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }

    protected function getMethod (string $method) : \ReflectionMethod
    {
        return $this->getMethodFromClass($method, ModuleManager::class);
    }
}
