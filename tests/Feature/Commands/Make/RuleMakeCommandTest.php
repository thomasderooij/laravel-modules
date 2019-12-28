<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class RuleMakeCommandTest extends ModuleTest
{
    public function testMakingARuleWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a rule
        $rule = "NewRule";
        $this->artisan("make:rule", ["name" => $rule]);

        // I should have a rule in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Rules\\$rule"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Rules/$rule.php")));
    }

    public function testMakingARuleWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a rule with the module option
        $rule = "NewRule";
        $this->artisan("make:rule", ["name" => $rule, "--module" => $module]);

        // I should have a rule in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Rules\\$rule"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Rules/$rule.php")));
    }

    public function testMakingARuleWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a rule
        $rule = "NewRule";
        $this->artisan("make:rule", ["name" => $rule]);

        // I should have a rule in my app dir
        $this->assertTrue(class_exists("App\\Rules\\$rule"));
        $this->assertTrue(is_file(app_path("Rules/$rule.php")));
        unlink(app_path("Rules/$rule.php"));
    }

    public function testMakingARuleWithoutModulesInitialised () : void
    {
        // If I make a rule
        $rule = "NewRule";
        $this->artisan("make:rule", ["name" => $rule]);

        // I should have a rule in my app dir
        $this->assertTrue(class_exists("App\\Rules\\$rule"));
        $this->assertTrue(is_file(app_path("Rules/$rule.php")));
        unlink(app_path("Rules/$rule.php"));
    }
}
