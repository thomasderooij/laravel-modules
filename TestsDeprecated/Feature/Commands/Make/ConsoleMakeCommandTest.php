<?php

namespace TestsDeprecated\Feature\Commands\Make;

use Illuminate\Support\Composer;
use TestsDeprecated\Feature\Modules\ModuleTest;

class ConsoleMakeCommandTest extends ModuleTest
{
    public function testMakingACommandWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $module = "TestModule" ;
        $this->createModule($module);

        // And I make a migration
        $command = "NewCommand";
        $this->artisan("make:command", ["name" => $command]);

        // I should have a command in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Console\\Commands\\$command"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Console/Commands/$command.php")));
    }

    public function testMakingACommandWithTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        $this->moduleManager->clearWorkbench();

        // And I make a migration with the module option
        $command = "NewCommand";
        $this->artisan("make:command", ["name" => $command, "--module" => $module]);

        // I should have a command in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Console\\Commands\\$command"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Console/Commands/$command.php")));
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

        $this->moduleManager->clearWorkbench();

        // And I make a migration with the module option
        $command = "NewCommand";
        $this->artisan("make:command", ["name" => $command, "--module" => "vanilla"]);

        // I should have a command in my app dir
//        $this->assertTrue(class_exists("App\\Console\\Commands\\$command"));
        $this->assertTrue(is_file(app_path("Console/Commands/$command.php")));
        unlink(app_path("Console/Commands/$command.php"));
    }

    public function testMakingACommandWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a migration
        $command = "NewCommand";
        $this->artisan("make:command", ["name" => $command]);

        // I should have a command in my app dir
        $this->assertTrue(class_exists("App\\Console\\Commands\\$command"));
        $this->assertTrue(is_file(app_path("Console/Commands/$command.php")));
        unlink(app_path("Console/Commands/$command.php"));
    }

    public function testMakingACommandWithoutModulesInitialised () : void
    {
        // If I make a migration
        $command = "NewCommand";
        $this->artisan("make:command", ["name" => $command]);

        // I should have a command in my app dir
        $this->assertTrue(class_exists("App\\Console\\Commands\\$command"));
        $this->assertTrue(is_file(app_path("Console/Commands/$command.php")));
        unlink(app_path("Console/Commands/$command.php"));
    }
}
