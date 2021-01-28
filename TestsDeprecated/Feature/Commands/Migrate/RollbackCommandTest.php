<?php

namespace TestsDeprecated\Feature\Commands\Migrate;

use TestsDeprecated\Feature\Modules\ModuleTest;

class RollbackCommandTest extends ModuleTest
{
    public function testRollbackWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();
        $this->artisan("migrate");

        // And I have a 2 modules
        $firstModule = "FirstModule";
        $this->createModule($firstModule);
        $secondModule = "SecondModule";
        $this->createModule($secondModule);

        // And I have 4 migrations in my default migrations folder
        $migrations = $this->createMigrations(4);

        // And I have 2 migrations in my first module
        sleep(1);
        $firstModuleMigrations = $this->createMigrations(2, $firstModule);

        // And I have 1 migration on my second module
        sleep(1);
        $secondModuleMigrations = $this->createMigrations(1, $secondModule);

        $this->moduleManager->clearWorkbench();

        // And I run the migrations in order of vanilla, first, second
        $this->artisan("migrate");
        $this->artisan("migrate", ["--modules" => "$firstModule,$secondModule"]);

        // And I have nothing in my workbench
        $this->moduleManager->setWorkbench($firstModule);

        // And I do a rollback
        $this->artisan("migrate:rollback");

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondModuleMigrations)->each(function (string $migration) {
            unlink($migration);
        });

        // I expect the second module migrations to be reverted
        $this->assertSame($firstModule, \DB::table("migrations")->select(["module", "batch"])->groupBy(["module", "batch"])->orderBy("batch")->get()->last()->module);
    }

    public function testRollbackWithNoModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();
        $this->artisan("migrate");

        // And I have a 2 modules
        $firstModule = "FirstModule";
        $this->createModule($firstModule);
        $secondModule = "SecondModule";
        $this->createModule($secondModule);

        // And I have 4 migrations in my default migrations folder
        $migrations = $this->createMigrations(4);

        // And I have 2 migrations in my first module
        sleep(1);
        $firstModuleMigrations = $this->createMigrations(2, $firstModule);

        // And I have 1 migration on my second module
        sleep(1);
        $secondModuleMigrations = $this->createMigrations(1, $secondModule);

        // And I have nothing in my workbench
        $this->moduleManager->clearWorkbench();

        // And I run the migrations in order of vanilla, first, second
        $this->artisan("migrate");
        $this->artisan("migrate", ["--modules" => "$firstModule,$secondModule"]);

        // And I do a rollback
        $this->artisan("migrate:rollback");

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondModuleMigrations)->each(function (string $migration) {
            unlink($migration);
        });

        // I expect the second module migrations to be reverted
        $this->assertSame($firstModule, \DB::table("migrations")->select(["module", "batch"])->groupBy(["module", "batch"])->orderBy("batch")->get()->last()->module);
    }

    public function testRollbackWithoutModulesInitialised () : void
    {
        // If I have 4 migrations in my default migrations folder
        $migrations = $this->createMigrations(4);
        $this->migrate();

        // And I have 2 migrations in my first module
        sleep(1);
        $firstModuleMigrations = $this->createMigrations(2);
        $this->migrate();

        // And I have 1 migration on my second module
        sleep(1);
        $secondModuleMigrations = $this->createMigrations(1);
        $this->migrate();

        // And I do a rollback
        $this->artisan("migrate:rollback");

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondModuleMigrations)->each(function (string $migration) {
            unlink($migration);
        });

        // I expect the last migration to be reverted\
        $secondModuleMigrations->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(0, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });
    }
}
