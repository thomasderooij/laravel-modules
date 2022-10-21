<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Mockery;
use Mockery\MockInterface;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as ModuleManagerContract;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class CommandTest extends Test
{
    protected readonly MockInterface|ModuleManagerContract $moduleManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);

        // Get the active modules for the kernel
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
    }
}
