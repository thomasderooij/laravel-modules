<?php

namespace Tests\Feature\Commands\Migrate;

use Tests\Feature\Modules\ModuleTest;

class FreshCommandTest extends ModuleTest
{
    public function testMigrateFreshWithAModuleInTheWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

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
        $secondMigrationModules = $this->createMigrations(1, $secondModule);

        // And I have a module in my workbench
        $this->moduleManager->setWorkbench($firstModule);

        // If I do the migrate fresh command
        $this->artisan("migrate:fresh");

        // I expect the 4 migrations from the default to be migrated.
        $migrations->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // And I expect the other migrations to not be be migrated
        $firstModuleMigrations->merge($secondMigrationModules)->each(function ($migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(0, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondMigrationModules)->each(function (string $migration) {
            unlink($migration);
        });
    }

    public function testMigrateFreshWithTheModulesOption () : void
    {
        // If I initiate modules
        $this->initModules();

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
        $secondMigrationModules = $this->createMigrations(1, $secondModule);

        // If I do the migrate fresh command with the modules command
        $this->artisan("migrate:fresh", ["--modules" => $firstModule . "," . $secondModule]);

        // I expect all migrations from the default to be migrated.
        $migrations->merge($firstModuleMigrations)->merge($secondMigrationModules)->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondMigrationModules)->each(function (string $migration) {
            unlink($migration);
        });
    }

    public function testMigrateFreshWithoutAModuleInTheWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

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
        $secondMigrationModules = $this->createMigrations(1, $secondModule);

        // And I have no module in my workbench
        $this->moduleManager->clearWorkbench();

        // If I do the migrate fresh command
        $this->artisan("migrate:fresh");

        // I expect the 4 migrations from the default to be migrated.
        $migrations->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // And I expect the other migrations to not be be migrated
        $firstModuleMigrations->merge($secondMigrationModules)->each(function ($migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(0, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondMigrationModules)->each(function (string $migration) {
            unlink($migration);
        });
    }

    public function testMigrateFreshWithoutModuleInitialised () : void
    {
        // If I have 4 migrations in my default migrations folder
        $migrations = $this->createMigrations(4);

        // If I do the migrate fresh command
        $this->artisan("migrate:fresh");

        // I expect the 4 migrations from the default to be migrated.
        $migrations->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // Delete the migrations
        $migrations->each(function (string $migration) {
            unlink($migration);
        });
    }


}
