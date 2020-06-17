<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class TestMakeCommandTest extends ModuleTest
{
    public function testMakingATestWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule";
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a test
        $test = "NewTest";
        $this->artisan("make:test", ["name" => $test]);

        // I should have a test in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\tests\\Feature\\$test"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/tests/Feature/$test.php")));
    }

    public function testMakingATestWithTheUnitFlagAndAModuleInWorkbench () : void
    {

        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule";
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a test
        $test = "NewTest";
        $this->artisan("make:test", ["name" => $test, "--unit" => true]);

        // I should have a test in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\tests\\Feature\\$test"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/tests/Unit/$test.php")));
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

        // And I make a rule with the module option
        $test = "NewTest";
        $this->artisan("make:test", ["name" => $test, "--module" => "vanilla"]);

        // I should have a rule in my app dir
        $this->assertTrue(is_file(base_path("tests/Feature/$test.php")));
        unlink(base_path("tests/Feature/$test.php"));
    }

    public function testMakingATestWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule";
        $this->createModule($module);

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a test
        $test = "NewTest";
        $this->artisan("make:test", ["name" => $test]);

        // I should have a test in my app dir
//        $this->assertTrue(class_exists("App\\tests\\Feature\\$test"));
        $this->assertTrue(is_file(base_path("tests/Feature/$test.php")));
        unlink(base_path("tests/Feature/$test.php"));
    }

    public function testMakingATestWithTheUnitFlagAndWithoutAModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule";
        $this->createModule($module);

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a test
        $test = "NewTest";
        $this->artisan("make:test", ["name" => $test, "--unit" => true]);

        // I should have a test in my app dir
//        $this->assertTrue(class_exists("App\\tests\\Feature\\$test"));
        $this->assertTrue(is_file(base_path("tests/Unit/$test.php")));
        unlink(base_path("tests/Unit/$test.php"));
    }
}
