<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class FactoryMakeCommandTest extends ModuleTest
{
    public function testMakingAFactoryWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $module = "TestModule" ;
        $this->createModule($module);

        // And I make a factory
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory]);

        // I should have a factory in my module
//        $this->assertTrue(class_exists($factory));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/database/factories/$factory.php")));
    }

    public function testMakingAFactoryWithTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a factory with the module option
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory, "--module" => $module]);

        // I should have a factory in my module
//        $this->assertTrue(class_exists($factory));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/database/factories/$factory.php")));
    }

    public function testMakingAFactoryWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a factory
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory]);

        // I should have a factory in my app dir
//        $this->assertTrue(class_exists("$factory"));
        $this->assertTrue(is_file(database_path("factories/$factory.php")));
        unlink(database_path("factories/$factory.php"));
    }

    public function testMakingAFactoryWithoutModulesInitialised () : void
    {
        // If I make a factory
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory]);

        // I should have a factory in my app dir
//        $this->assertTrue(class_exists("$factory"));
        $this->assertTrue(is_file(database_path("factories/$factory.php")));
        unlink(database_path("factories/$factory.php"));
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

        // And I make a factory with the module option
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory, "--module" => "vanilla"]);

        // I should have a factory in my app dir
//        $this->assertTrue(class_exists("$factory"));
        $this->assertTrue(is_file(database_path("factories/$factory.php")));
        unlink(database_path("factories/$factory.php"));
    }
}
