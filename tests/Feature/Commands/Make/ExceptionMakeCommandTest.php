<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class ExceptionMakeCommandTest extends ModuleTest
{
    public function testMakingAnExceptionWithModuleInTheWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a migration
        $exception = "NewException";
        $this->artisan("make:exception", ["name" => $exception]);

        // I should have a exception in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Exceptions\\$exception"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Exceptions/$exception.php")));
    }

    public function testMakingAnExceptionWithTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a migration with the module option
        $exception = "NewException";
        $this->artisan("make:exception", ["name" => $exception, "--module" => $module]);

        // I should have a exception in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Exceptions\\$exception"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Exceptions/$exception.php")));
    }

    public function testMakingAnExceptionWithoutModuleInTheWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a migration
        $exception = "NewException";
        $this->artisan("make:exception", ["name" => $exception]);

        // I should have an exception in my app dir
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Exceptions\\$exception"));
        $this->assertTrue(is_file(app_path("Exceptions/$exception.php")));
        unlink(app_path("Exceptions/$exception.php"));
    }

    public function testMakingAnExceptionWithoutModulesInitialised () : void
    {
        // If I make a migration
        $exception = "NewException";
        $this->artisan("make:exception", ["name" => $exception]);

        // I should have an exception in my app dir
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Exceptions\\$exception"));
        $this->assertTrue(is_file(app_path("Exceptions/$exception.php")));
        unlink(app_path("Exceptions/$exception.php"));
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
        $exception = "NewException";
        $this->artisan("make:exception", ["name" => $exception, "--module" => "vanilla"]);

        // I should have an exception in my app dir
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Exceptions\\$exception"));
        $this->assertTrue(is_file(app_path("Exceptions/$exception.php")));
        unlink(app_path("Exceptions/$exception.php"));
    }
}
