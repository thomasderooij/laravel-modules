<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleStateRepository;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class ModuleStateRepositoryTest extends Test
{
    protected $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance('files', $this->filesystem);
    }

    /**
     * @return Mockery\MockInterface&ModuleStateRepository
     */
    protected function getMockRepository(string $method): Mockery\MockInterface
    {
        $mockMethods = $this->getMockableClassMethods(ModuleStateRepository::class, $method);

        $functions = implode(", ", $mockMethods);
        // Mock the module manager with all functions except the one we want to test
        $mock = Mockery::mock(ModuleStateRepository::class . "[$functions]", [$this->filesystem]);
        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }

    protected function getMethod(string $method): \ReflectionMethod
    {
        return $this->getMethodFromClass($method, ModuleStateRepository::class);
    }
}
