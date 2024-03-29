<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Migrate;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Events\Dispatcher;
use Mockery;
use Thomasderooij\LaravelModules\Console\Kernel as ModulesKernel;
use Thomasderooij\LaravelModules\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Services\ModuleMigrator;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class MigrateTest extends Test
{
    protected $moduleManager;
    protected $dependencyHandler;
    protected $migrator;
    protected $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
        $this->dependencyHandler = Mockery::mock(DependencyHandler::class);
        $this->instance("module.service.dependency_handler", $this->dependencyHandler);
        $this->migrator = Mockery::mock(ModuleMigrator::class);
        $this->instance("migrator", $this->migrator);
        $this->dispatcher = Mockery::mock(Dispatcher::class);
        $this->instance("events", $this->dispatcher);

        // This is for the composite kernels to see what should be used and what not
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        // And the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // And the dispatcher should receive a dispatch call
        $this->dispatcher->shouldReceive('dispatch');
    }
}
