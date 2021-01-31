<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class ModuleManagerTest extends Test
{
    protected $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance('files', $this->filesystem);
    }

    /**
     * @param array $mockFunctions
     * @return Mockery\MockInterface&ModuleManager
     */
    protected function getMockManager (string $method) : Mockery\MockInterface
    {
        $mockMethods = $this->getClassMethods(ModuleManager::class, $method);

        $functions = implode(", ", $mockMethods);
        // Mock the module manager with all functions except the one we want to test
        $mock = Mockery::mock(ModuleManager::class."[$functions]", [$this->filesystem]);
        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }

    protected function getMethod (string $method) : \ReflectionMethod
    {
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($method);
        $uut->setAccessible(true);

        return $uut;
    }
}
