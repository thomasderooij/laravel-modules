<?php

namespace TestsDeprecated\Feature\Commands\Make;

use TestsDeprecated\Feature\Modules\ModuleTest;

class ModelMakeCommandTest extends ModuleTest
{
    public function testMakingAMigrationWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a migration
        $model = "newModel";
        $this->artisan("make:model", ["name" => $model]);

        // I should have a model in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . $model));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/$model.php")));
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
        $model = "newModel";
        $this->artisan("make:model", ["name" => $model, "--module" => $module]);

        // I should have a model in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . $model));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/$model.php")));
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
        $model = "newModel";
        $this->artisan("make:model", ["name" => $model, "--module" => "vanilla"]);

        // I should have a model in my app dir
//        $this->assertTrue(class_exists("App\\$model"));
        $this->assertTrue(is_file(app_path($model.".php")));
        unlink(app_path($model.".php"));
    }

    public function testMakingAMigrationWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule";
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->clearWorkbench();

        // And I make a migration
        $model = "NewModel";
        $this->artisan("make:model", ["name" => $model]);

        // I should have a model in my app dir
        $this->assertTrue(class_exists("App\\$model"));
        $this->assertTrue(is_file(app_path($model.".php")));
        unlink(app_path($model.".php"));
    }

    public function testMakingAModelWithoutModulesInitialised () : void
    {
        // If I make a migration
        $model = "NewModel";
        $this->artisan("make:model", ["name" => $model]);

        // I should have a model in my app dir
        $this->assertTrue(class_exists("App\\$model"));
        $this->assertTrue(is_file(app_path($model.".php")));
        unlink(app_path($model.".php"));
    }
}
