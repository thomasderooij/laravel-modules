<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrationRepository;

use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Query\Builder;
use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleMigrationRepository;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class ModuleMigrationRepositoryTest extends Test
{
    protected $method;
    protected $repository;
    protected $uut;
    protected $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getRepository();
        $this->uut = $this->getMethodFromClass($this->method, ModuleMigrationRepository::class);

        $this->builder = \Mockery::mock(Builder::class);
    }

    /**
     * @return Mockery\MockInterface&ModuleMigrationRepository
     */
    protected function getRepository(): Mockery\MockInterface
    {
        $mockableMethods = $this->getMockableClassMethods(ModuleMigrationRepository::class, $this->method);

        $methodString = implode(',', $mockableMethods);
        $mock = Mockery::mock(ModuleMigrationRepository::class . "[$methodString]", [
            $this->app->make(ConnectionResolver::class),
            "table"
        ]);
        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }
}
