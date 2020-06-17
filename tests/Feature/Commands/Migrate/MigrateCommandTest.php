<?php

namespace Tests\Feature\Commands\Migrate;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Modules\ModuleTest;

class MigrateCommandTest extends ModuleTest
{
    public function testMigratingWithAModuleInWorkbench () : void
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

        // And I have my first module in my workbench
        $this->moduleManager->setWorkbench($firstModule);

        // And I call migrate
        $this->artisan("migrate");

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondModuleMigrations)->each(function (string $migration) {
            unlink($migration);
        });

        // I expect the first module to be migrated
        // And I expect the migration to be marked by module
        $firstModuleMigrations->each(function (string $migration) use ($firstModule) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);

            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
            $this->assertSame($firstModule, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->first()->module);
        });

        // And I expect the modules in the default directory and the second module not to be migrated
        $migrations->merge($secondModule)->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(0, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });
    }

    // This triggers an autoload error, and I have no clue why and I don't feel like figuring it out. I'll save this one for later
    public function testMigratingWithTheModulesOption () : void
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

        // And I have my first module in my workbench
        $this->moduleManager->clearWorkbench();

        // And I call migrate
        $this->artisan("migrate", ["--modules" => $firstModule . "," . $secondModule]);

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondModuleMigrations)->each(function (string $migration) {
            unlink($migration);
        });

        // I expect the first module to be migrated
        $firstModuleMigrations->merge($secondModuleMigrations)->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // And I expect the modules in the default directory and the second module not to be migrated
        $migrations->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(0, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });
    }

    public function testMigratingWithTheVanillaModule () : void
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

        // And I have my first module in my workbench
        $this->moduleManager->clearWorkbench();

        // And I call migrate
        $this->artisan("migrate", ["--modules" => "Vanilla," . $firstModule . "," . $secondModule]);

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondModuleMigrations)->each(function (string $migration) {
            unlink($migration);
        });

        // I expect the first module to be migrated
        $firstModuleMigrations->merge($secondModuleMigrations)->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // And I expect the modules in the default directory and the second module also to be migrated
        $migrations->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });
    }

    public function testMigratingWithoutAModuleInWorkbench () : void
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
        // This is the "Vanilla" module
        $migrations = $this->createMigrations(4);

        // And I have 2 migrations in my first module
        sleep(1);
        $firstModuleMigrations = $this->createMigrations(2, $firstModule);

        // And I have 1 migration on my second module
        sleep(1);
        $secondModuleMigrations = $this->createMigrations(1, $secondModule);

        // And I have my first module in my workbench
        $this->moduleManager->clearWorkbench();

        // And I call migrate
        $this->artisan("migrate");

        // Delete the migrations
        $migrations->merge($firstModuleMigrations)->merge($secondModuleMigrations)->each(function (string $migration) {
            unlink($migration);
        });

        // I expect the default migrations to be migrated
        $migrations->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });

        // And I expect the modules in the default directory and the second module not to be migrated
        $firstModuleMigrations->merge($secondModule)->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(0, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });
    }

    public function testMigratingWithoutHavingModulesInitialised () : void
    {
        // If I have 4 migrations in my default migrations folder
        $migrations = $this->createMigrations(4);

        // And I call migrate
        $this->artisan("migrate");

        // Delete the migrations
        $migrations->each(function (string $migration) {
            unlink($migration);
        });

        // I expect the four migrations to be migrated
        $migrations->each(function (string $migration) {
            $explosion = explode("/", $migration);
            $migrationFileName = array_pop($explosion);
            $this->assertSame(1, \DB::table("migrations")->where("migration", "=", substr($migrationFileName, 0, strlen($migrationFileName) - 4))->count());
        });
    }
}
