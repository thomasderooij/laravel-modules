<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Console\CompositeKernel as ConsoleKernel;
use Thomasderooij\LaravelModules\Contracts\ConsoleCompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Factories\AppBootstrapFactory as AppBootstrapFactoryContract;
use Thomasderooij\LaravelModules\Contracts\HttpCompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as ModuleManagerContract;
use Thomasderooij\LaravelModules\Factories\AppBootstrapFactory;
use Thomasderooij\LaravelModules\Http\CompositeKernel as HttpKernel;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class AppBootstrapFactoryTest extends Test
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance(ModuleManagerContract::class, $this->app->make(ModuleManager::class));
        $this->instance(ConsoleCompositeKernel::class, $this->app->make(ConsoleKernel::class));
        $this->instance(HttpCompositeKernel::class, $this->app->make(HttpKernel::class));
    }

    public function testCreate () : void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $filesystem = $this->app->make('files');
        $this->instance(Filesystem::class, $mockFilesystem);

        // If I have a app bootstrap factory
        /** @var AppBootstrapFactoryContract $uut $uut */
        $uut = $this->app->make(AppBootstrapFactory::class);

        // The original bootstrap file should rename to app_orig.php
        $mockFilesystem
            ->shouldReceive('move')
            ->withArgs([
                base_path("bootstrap/app.php"),
                base_path("bootstrap/app_orig.php")
            ])
            ->andReturn(true)
            ->once()
        ;

        // A stub file should be fetched
        $stub = realpath(__DIR__ . '/../../src/Factories/stubs/bootstrapFile.stub');
        $mockFilesystem
            ->shouldReceive('get')
            ->withArgs([$stub])
            ->andReturn([$filesystem->get($stub)]) // Here we use the real filesystem to output the actual contents of the stub
            ->once()
        ;

        // And a new bootstrap file should be created, based on a stub
        $argumentContent = null; // This variable will be used to capture the argument content
        $mockFilesystem
            ->shouldReceive('put')
            ->withArgs([
                base_path("bootstrap/app.php"),
                Mockery::capture($argumentContent)
            ])
            ->once()
        ;

        // Call the create function
        $uut->create("MyNamespace");
        // Check if the captured put arguments matches the snapshot
        $this->assertMatchesSnapshot($argumentContent);
    }

    public function testUndo () : void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $this->instance(Filesystem::class, $mockFilesystem);

        // If I have a bootstrap factory
        /** @var AppBootstrapFactoryContract $uut $uut */
        $uut = $this->app->make(AppBootstrapFactory::class);

        // The boostrap file should be removed
        $mockFilesystem
            ->shouldReceive('delete')
            ->withArgs([base_path("bootstrap/app.php")])
            ->andReturn([true])
            ->once()
        ;

        // And the original bootstrap file should take its place
        $mockFilesystem
            ->shouldReceive('move')
            ->withArgs([
                base_path("bootstrap/app_orig.php"),
                base_path("bootstrap/app.php")
            ])
            ->andReturn([true])
            ->once()
        ;

        $uut->undo();
    }
}
