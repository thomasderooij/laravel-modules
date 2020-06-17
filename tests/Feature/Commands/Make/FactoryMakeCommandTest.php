<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class FactoryMakeCommandTest extends ModuleTest
{
    public function testMakingAChannelWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a factory
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory]);

        // I should have a factory in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/database/factories/$factory.php")));
        unlink(base_path(config("modules.root") . "/{$this->module}/database/factories/$factory.php"));
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

        // And I make a factory
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory, "--module" => $module]);

        // I should have a factory in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/database/factories/$factory.php")));
        unlink(base_path(config("modules.root") . "/{$this->module}/database/factories/$factory.php"));
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

    public function testMakingAChannelWithoutModuleInWorkbench () : void
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

        // I should have a factory in my database directory
        $this->assertTrue(is_file(base_path("database/factories/$factory.php")));
        unlink(base_path("database/factories/$factory.php"));
    }

    public function testMakingAChannelResourceWithoutModulesInitialised () : void
    {
        // If I make a factory
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory]);

        // I should have a factory in my database directory
        $this->assertTrue(is_file(base_path("database/factories/$factory.php")));
        unlink(base_path("database/factories/$factory.php"));
    }

    public function testICannotMakeTwoFactoriesWithTheSameName () : void
    {

    }
}
