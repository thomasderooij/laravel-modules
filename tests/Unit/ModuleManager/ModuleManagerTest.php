<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class ModuleManagerTest extends Test
{
    /**
     * @return Mockery\MockInterface&Filesystem
     */
    protected function getMockFilesystem () : Mockery\MockInterface
    {
        $filesystem = Mockery::mock(Filesystem::class);
        $this->instance('files', $filesystem);

        return $filesystem;
    }

    /**
     * @param Filesystem|null $filesystem
     * @param array $mockFunctions
     * @return Mockery\MockInterface&ModuleManager
     */
    protected function getMockManager (Filesystem $filesystem = null, string $uut) : Mockery\MockInterface
    {
        $reflection = new \ReflectionClass(ModuleManager::class);
        // Get all the methods from out module manager
        $methods = $reflection->getMethods();
        $mockFunctions = array_map(function (\ReflectionMethod $method) { return $method->getName(); }, $methods);
        // Remove the constructor
        $constructorPosition = array_search("__construct", $mockFunctions);
        unset($mockFunctions[$constructorPosition]);
        // And remove the function we want to test
        $testUnitPosition = array_search($uut, $mockFunctions);
        if ($testUnitPosition === false) {
            throw new \Exception("That function does not exist in the ModuleManager class.");
        }
        unset($mockFunctions[$testUnitPosition]);

        if ($filesystem === null) {
            $filesystem = $this->getMockFilesystem();
        }

        $functions = implode(", ", $mockFunctions);
        // Mock the module manager with all functions except the one we want to test
        $mock = Mockery::mock(ModuleManager::class."[$functions]", [$filesystem]);
        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }
}
