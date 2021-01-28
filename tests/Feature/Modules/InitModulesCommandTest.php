<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Modules;

use Mockery;
use Thomasderooij\LaravelModules\Console\Commands\InitModuleCommand;
use Thomasderooij\LaravelModules\Contracts\Factories\AppBootstrapFactory as AppBootstrapFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleMigrationFactory as ModuleMigrationFactoryContract;
use Thomasderooij\LaravelModules\Factories\AppBootstrapFactory;
use Thomasderooij\LaravelModules\Factories\ModuleMigrationFactory;
use Thomasderooij\LaravelModules\Tests\Test;

class InitModulesCommandTest extends Test
{
    private $root = 'test_root';

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @group uut
     */
    public function testInitiatingModules () : void
    {
        $mockBootstrapFactory = Mockery::mock(AppBootstrapFactory::class);
        $this->instance(AppBootstrapFactoryContract::class, $mockBootstrapFactory);
        $mockMigrationFactory = Mockery::mock(ModuleMigrationFactory::class);
        $this->instance(ModuleMigrationFactoryContract::class, $mockMigrationFactory);
        $command = $this->app->make(InitModuleCommand::class);
        dd($command);

        // When I run the init command
        $response = $this->artisan("module:init");
        // I expect to be asked which directory will be my modules directory
        $response->expectsQuestion("What will be the root directory of your modules?", $this->root);
        // And I expect to receive instructions after a successful initialisation
        $response->expectsOutput("You are set to go. Make sure to run migration command to get your module migrations working.");
        $response->expectsOutput("Call for module:new your-module-name-here to create a module. For any other info, check out the readme.md file.");

        // In this process, the bootstrap factory create method should be called
        $mockBootstrapFactory->shouldReceive('create')->once();

        // And the migration factory create method should be called
        $mockMigrationFactory->shouldReceive('create')->once();
    }
}
