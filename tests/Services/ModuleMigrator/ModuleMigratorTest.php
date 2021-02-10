<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrator;

use Illuminate\Database\ConnectionResolver;
use Illuminate\Events\Dispatcher;
use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleMigrationRepository;
use Thomasderooij\LaravelModules\Services\ModuleMigrator;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class ModuleMigratorTest extends Test
{
    protected $method;
    protected $migrator;
    protected $repository;
    protected $uut;

    protected function setUp(): void
    {
        parent::setUp();

        // We mock the migration repository
        $this->repository = Mockery::mock(ModuleMigrationRepository::class);

        // And create a partial mock for the migrator
        $mockMethods = $this->getMockableClassMethods(ModuleMigrator::class, $this->method);
        $methodString = implode(",", $mockMethods);
        $this->migrator = Mockery::mock(ModuleMigrator::class."[$methodString]", [
            $this->repository,
            $this->app->make(ConnectionResolver::class),
            $this->app->make("files"),
            $this->app->make(Dispatcher::class)
        ]);
        $this->migrator->shouldAllowMockingProtectedMethods();

        // And we get our method abstract
        $this->uut = $this->getMethodFromClass($this->method, ModuleMigrator::class);
    }

}
