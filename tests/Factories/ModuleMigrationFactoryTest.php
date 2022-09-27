<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleMigrationFactory as ModuleMigrationFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as ModuleManagerContract;
use Thomasderooij\LaravelModules\Factories\ModuleMigrationFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class ModuleMigrationFactoryTest extends Test
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance(ModuleManagerContract::class, $this->app->make(ModuleManager::class));
    }

    public function testCreate(): void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $filesystem = $this->app->make('files');
        $this->instance(Filesystem::class, $mockFilesystem);

        // I have a migration factory
        /** @var ModuleMigrationFactoryContract $uut */
        $uut = $this->app->make(ModuleMigrationFactory::class);

        // A stub file should be fetched
        $stub = realpath(__DIR__ . '/../../src/Factories/stubs/moduleMigration.stub');
        $mockFilesystem
            ->shouldReceive('get')
            ->withArgs([$stub])
            ->andReturn([$filesystem->get($stub)]
            ) // Here we use the real filesystem to output the actual contents of the stub
            ->once();

        // And a new migration file should be created, based on a stub
        $argumentContent = null; // This variable will be used to capture the argument content
        $mockFilesystem
            ->shouldReceive('put')
            ->withArgs([
                base_path("database/migrations/2010_11_01_000000_module_init_migration.php"),
                Mockery::capture($argumentContent)
            ])
            ->once();

        // When I call the create function
        $uut->create();
        // Check if the captured put arguments matches the snapshot
        $this->assertMatchesSnapshot($argumentContent);
    }

    public function testUndo(): void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $this->instance(Filesystem::class, $mockFilesystem);

        // I have a migration factory
        /** @var ModuleMigrationFactoryContract $uut */
        $uut = $this->app->make(ModuleMigrationFactory::class);

        // The filesysten should check if the file it wants to delete is actually a file
        $file = base_path("database/migrations/2010_11_01_000000_module_init_migration.php");
        $mockFilesystem->shouldReceive("isFile")->withArgs([$file])->andReturn(true);
        // And then delete it
        $mockFilesystem
            ->shouldReceive('delete')
            ->withArgs([$file])
            ->once();

        // When I call the undo function
        $uut->undo();
    }

    public function testUndoIfThereIsNothingToUndo(): void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $this->instance(Filesystem::class, $mockFilesystem);

        // I have a migration factory
        /** @var ModuleMigrationFactoryContract $uut */
        $uut = $this->app->make(ModuleMigrationFactory::class);

        // The filesysten should check if the file it wants to delete is actually a file
        $file = base_path("database/migrations/2010_11_01_000000_module_init_migration.php");
        $mockFilesystem->shouldReceive("isFile")->withArgs([$file])->andReturn(false);
        // And that should be the end of it

        $uut->undo();
    }
}
