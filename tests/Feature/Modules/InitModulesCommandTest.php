<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Modules;

use Illuminate\Support\Composer;
use Mockery;
use Thomasderooij\LaravelModules\Console\Commands\InitModuleCommand;
use Thomasderooij\LaravelModules\Contracts\Factories\AppBootstrapFactory as AppBootstrapFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\ConfigFactory as ConfigFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleMigrationFactory as ModuleMigrationFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as ModuleManagerContract;
use Thomasderooij\LaravelModules\Factories\AppBootstrapFactory;
use Thomasderooij\LaravelModules\Factories\ConfigFactory;
use Thomasderooij\LaravelModules\Factories\ModuleMigrationFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class InitModulesCommandTest extends Test
{
    private $root = 'test_root';

    private $bootstrapFactory;
    private $composer;
    private $configFactory;
    private $migrationFactory;
    private $moduleManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapFactory = Mockery::mock(AppBootstrapFactory::class);
        $this->instance('module.factory.bootstrap', $this->bootstrapFactory);
        $this->composer = Mockery::mock(Composer::class);
        $this->instance("composer", $this->composer);
        $this->configFactory = Mockery::mock(ConfigFactory::class);
        $this->instance('module.factory.config', $this->configFactory);
        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance('module.service.manager', $this->moduleManager);
        $this->migrationFactory = Mockery::mock(ModuleMigrationFactory::class);
        $this->instance('module.factory.migration', $this->migrationFactory);
    }

    /**
     * @group uut
     */
    public function testInitiatingModules () : void
    {
        // When I run the init command
        $response = $this->artisan("module:init");
        // I expect to be asked which directory will be my modules directory
        $response->expectsQuestion("What will be the root directory of your modules?", $this->root);
        // And I expect to receive instructions after a successful initialisation
        $response->expectsOutput("You are set to go. Make sure to run migration command to get your module migrations working.");
        $response->expectsOutput("Call for module:new your-module-name-here to create a module. For any other info, check out the readme.md file.");

        // The artisan service provider is going to ask for a workbench, so that should return null
        $this->moduleManager->shouldReceive('getWorkBench')->andReturn(null);

        // In this process, the bootstrap factory create method should be called
        $this->bootstrapFactory->shouldReceive('create')->once();

        // And the config factory create method should be called
        $this->configFactory->shouldReceive('create')->withArgs([$this->root])->once();

        // And the migration factory create method should be called
        $this->migrationFactory->shouldReceive('create')->once();

        $this->moduleManager->shouldReceive('isInitialised')->andReturn(false)->once();

        // And composer should be trigger
        $this->composer->shouldReceive('dumpAutoloads')->once();

        $response->run();
    }
}
