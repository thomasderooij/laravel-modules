<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrator;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Database\Events\MigrationsStarted;
use Mockery;

class RunPendingTest extends ModuleMigratorTest
{
    public function setUp(): void
    {
        $this->method = "runPending";

        parent::setUp();
    }

    public function testWithoutModules(): void
    {
        // If there is no module
        $options = [];
        $migrations = [$file1 = "migrations_1", $file2 = "migration_2"];

        // We should get a batch number
        $this->repository->shouldReceive("getNextBatchNumber")->andReturn($nextBatchNumber = 42);
        // Fire an event
        $this->migrator->shouldReceive("fireMigrationEvent")->withArgs([
            Mockery::on(function ($argument) {
                return $argument instanceof MigrationsStarted;
            })
        ]);
        // It should then run up
        $this->migrator->shouldReceive("runUp")->withArgs([$file1, $nextBatchNumber, false, null]);
        $this->migrator->shouldReceive("runUp")->withArgs([$file2, $nextBatchNumber, false, null]);
        // And then fire a migrations ended event
        $this->migrator->shouldReceive("fireMigrationEvent")->withArgs([
            Mockery::on(function ($argument) {
                return $argument instanceof MigrationsEnded;
            })
        ]);

        $this->uut->invoke($this->migrator, $migrations, $options);
    }

    public function testWithModules(): void
    {
        // If there is a module
        $options = ["module" => $module = "MyModule"];
        $migrations = [$file1 = "migrations_1", $file2 = "migration_2"];

        // We should get a batch number
        $this->repository->shouldReceive("getNextBatchNumber")->andReturn($nextBatchNumber = 42);
        // Fire an event
        $this->migrator->shouldReceive("fireMigrationEvent")->withArgs([
            Mockery::on(function ($argument) {
                return $argument instanceof MigrationsStarted;
            })
        ]);
        // It should then run up
        $this->migrator->shouldReceive("runUp")->withArgs([$file1, $nextBatchNumber, false, $module]);
        $this->migrator->shouldReceive("runUp")->withArgs([$file2, $nextBatchNumber, false, $module]);
        // And then fire a migrations ended event
        $this->migrator->shouldReceive("fireMigrationEvent")->withArgs([
            Mockery::on(function ($argument) {
                return $argument instanceof MigrationsEnded;
            })
        ]);

        $this->uut->invoke($this->migrator, $migrations, $options);
    }
}
