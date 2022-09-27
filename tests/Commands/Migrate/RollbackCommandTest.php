<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Migrate;

use Mockery;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate\RollbackCommand;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Services\ModuleMigrator;

class RollbackCommandTest extends MigrateTest
{
    public function testGetMigrationPathsWithoutModule(): void
    {
        // If I have a command
        $migrator = Mockery::mock(ModuleMigrator::class);
        $moduleManager = Mockery::mock(ModuleManager::class);
        $command = Mockery::mock(
            RollbackCommand::class . "[parentCall, getLastMigrationModule, getMigrationPathByModule]",
            [
                $migrator,
                $moduleManager
            ]
        );
        $command->shouldAllowMockingProtectedMethods();

        // It should call getLastMigrationModule and return nothing
        $command->shouldReceive("getLastMigrationModule")->andReturn(null);
        // And then call its parent function
        $command->shouldReceive("parentCall")->withArgs(["getMigrationPaths"])->andReturn($expected = "result");

        // And that result should be returned
        $uut = $this->getMethodFromClass("getMigrationPaths", RollbackCommand::class);
        $result = $uut->invoke($command);

        $this->assertSame($expected, $result);
    }

    public function testGetMigrationPathsWithModule(): void
    {
        // If I have a command
        $migrator = Mockery::mock(ModuleMigrator::class);
        $moduleManager = Mockery::mock(ModuleManager::class);
        $command = Mockery::mock(
            RollbackCommand::class . "[parentCall, getLastMigrationModule, getMigrationPathByModule]",
            [
                $migrator,
                $moduleManager
            ]
        );
        $command->shouldAllowMockingProtectedMethods();

        // It should call getLastMigrationModule and return nothing
        $command->shouldReceive("getLastMigrationModule")->andReturn($module = "MyModule");
        // And then call its parent function
        $command->shouldReceive("getMigrationPathByModule")->withArgs([$module])->andReturn($expected = "result");

        // And that result should be returned
        $uut = $this->getMethodFromClass("getMigrationPaths", RollbackCommand::class);
        $result = $uut->invoke($command);

        $this->assertSame($expected, $result);
    }
}
