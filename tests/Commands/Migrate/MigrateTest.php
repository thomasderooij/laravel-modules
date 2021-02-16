<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Migrate;

use Illuminate\Events\Dispatcher;
use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Services\ModuleMigrator;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class MigrateTest extends Test
{
    protected $moduleManager;
    protected $migrator;
    protected $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
        $this->migrator = Mockery::mock(ModuleMigrator::class);
        $this->instance("migrator", $this->migrator);
        $this->dispatcher = Mockery::mock(Dispatcher::class);
        $this->instance("events", $this->dispatcher);

        // This is for the composite kernels to see what should be used and what not
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        // And the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
    }
}
