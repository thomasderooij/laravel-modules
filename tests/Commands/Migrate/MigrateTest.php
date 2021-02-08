<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Migrate;

use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Services\ModuleMigrator;
use Thomasderooij\LaravelModules\Tests\Commands\InitialisedModulesTest;

abstract class MigrateTest extends InitialisedModulesTest
{
    protected $moduleManager;
    protected $migrator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
        $this->migrator = Mockery::mock(ModuleMigrator::class);
        $this->instance("migrator", $this->migrator);

        // This is for the composite kernels to see what should be used and what not
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn([]);
        // And the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
    }
}
