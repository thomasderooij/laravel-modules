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
    protected function getMockManager (Filesystem $filesystem = null, array $mockFunctions = []) : Mockery\MockInterface
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
