<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\Migrations\Migrator;
use Thomasderooij\LaravelModules\ParentCallTrait;

class ModuleMigrator extends Migrator
{
    use ParentCallTrait;

    /**
     * Run an array of migrations.
     *
     * @param array $migrations
     * @param array $options
     */
    public function runPending(array $migrations, array $options = []): void
    {
        // First we will just make sure that there are any migrations to run. If there
        // aren't, we will just make a note of it to the developer so they're aware
        // that all the migrations have been run against this database system.
        if (count($migrations) === 0) {
            $this->note('<info>Nothing to migrate.</info>');

            return;
        }

        // Next, we will get the next batch number for the migrations so we can insert
        // correct batch number in the database migrations repository when we store
        // each migration's execution. We will also extract a few of the options.
        $batch = $this->repository->getNextBatchNumber();

        $pretend = $options['pretend'] ?? false;

        $step = $options['step'] ?? false;

        $module = $options["module"] ?? null;

        $this->fireMigrationEvent(new MigrationsStarted("up"));

        // Once we have the array of migrations, we will spin through them and run the
        // migrations "up" so the changes are made to the databases. We'll then log
        // that the migration was run so we don't repeat it next time we execute.
        foreach ($migrations as $file) {
            $this->runUp($file, $batch, $pretend, $module);

            if ($step) {
                $batch++;
            }
        }

        $this->fireMigrationEvent(new MigrationsEnded("up"));
    }

    /**
     * Run "up" a migration instance.
     *
     * @param string $file
     * @param int $batch
     * @param bool $pretend
     * @param null|string $module
     */
    public function runUp($file, $batch, $pretend, string $module = null): void
    {
        if ($module === null) {
            $this->parentCall("runUp", [$file, $batch, $pretend]);
            return;
        }

        $this->baseRunUpFunction($file, $batch, $pretend, $module);
    }

    /**
     * @param $file
     * @param $batch
     * @param $pretend
     * @param null|string $module
     */
    protected function baseRunUpFunction($file, $batch, $pretend, string $module = null)
    {
        $migration = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        if ($pretend) {
            $this->pretendToRun($migration, 'up');
        }

        $this->note("<comment>Migrating:</comment> {$name}");

        $startTime = microtime(true);

        $this->runMigration($migration, 'up');

        $runTime = round(microtime(true) - $startTime, 2);

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->logInRepository($name, $batch, $module);

        $this->note("<info>Migrated:</info>  {$name} ({$runTime} seconds)");
    }

    /**
     * Log the migration in the repository
     *
     * @param string $name
     * @param int $batch
     * @param string|null $module
     */
    protected function logInRepository(string $name, int $batch, string $module = null)
    {
        if ($module === null) {
            $this->repository->log($name, $batch);
            return;
        }

        $this->repository->log($name, $batch, $module);
    }
}
