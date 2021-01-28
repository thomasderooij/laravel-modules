<?php

namespace TestsDeprecated\Feature\Commands\Make;

use TestsDeprecated\Feature\Modules\ModuleTest;

class MigrateMakeCommandTest extends ModuleTest
{
    public function testMakingAMigrationWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a migration
        $migration = "create_new_table";
        $this->artisan("make:migration", ["name" => $migration]);

        // I should have a migration file in my module
        $base = base_path(config("modules.root")."/".$this->moduleManager->getWorkBench()."/database/migrations");
        $dirContents = scandir($base);
        $file = $base."/".array_pop($dirContents);
        $this->assertNotNull(strpos($file, $migration));
        unlink($file);

    }

    public function testUsingTheVanillaOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a migration with the module option
        $migration = "create_new_table";
        $this->artisan("make:migration", ["name" => $migration, "--module" => "vanilla"]);

        // I should have a migration file in my database dir
        $base = base_path("/database/migrations");
        $dirContents = scandir($base);
        $file = $base."/".array_pop($dirContents);
        $this->assertNotNull(strpos($file, $migration));
        unlink($file);
    }

    public function testMakingAMigrationWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a migration with the module option
        $migration = "create_new_table";
        $this->artisan("make:migration", ["name" => $migration, "--module" => $module]);

        // I should have a migration file in my module
        $base = base_path(config("modules.root")."/".$module."/database/migrations");
        $dirContents = scandir($base);
        $file = $base."/".array_pop($dirContents);
        $this->assertNotNull(strpos($file, $migration));
    }

    public function testMakingAMigrationWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And there is not module set to my workbench
        $this->moduleManager->clearWorkbench();

        // And I make a migration
        $migration = "create_new_table";
        $this->artisan("make:migration", ["name" => $migration]);

        // I should have a migration file in my database dir
        $base = base_path("/database/migrations");
        $dirContents = scandir($base);
        $file = $base."/".array_pop($dirContents);
        $this->assertNotNull(strpos($file, $migration));
        unlink($file);
    }

    public function testMakingAMigrationWithoutModulesInitialised () : void
    {
        // If I make a migration
        $migration = "create_new_table";
        $this->artisan("make:migration", ["name" => $migration]);

        // I should have a migration file in my database dir
        $base = base_path("/database/migrations");
        $dirContents = scandir($base);
        $file = $base."/".array_pop($dirContents);
        $this->assertNotNull(strpos($file, $migration));
        unlink($file);
    }
}
