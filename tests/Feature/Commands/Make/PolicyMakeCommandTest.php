<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class PolicyMakeCommandTest extends ModuleTest
{
    public function testMakingAPolicyWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a policy
        $policy = "NewPolicy";
        $response = $this->artisan("make:policy", ["name" => $policy]);
        $response->expectsOutput("Policy created successfully.")->run();

        // I should have a policy in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Policies\\$policy"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Policies/$policy.php")));
    }

    public function testMakingAPolicyWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a policy with the module option
        $policy = "NewPolicy";
        $response = $this->artisan("make:policy", ["name" => $policy, "--module" => $module]);
        $response->expectsOutput("Policy created successfully.")->run();

        // I should have a policy in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Policies\\$policy"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Policies/$policy.php")));
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

        // And I make a policy with the module option
        $policy = "NewPolicy";
        $response = $this->artisan("make:policy", ["name" => $policy, "--module" => "vanilla"]);
        $response->expectsOutput("Policy created successfully.")->run();

        // I should have a policy in my app dir
//        $this->assertTrue(class_exists("App\\Policies\\$policy"));
        $this->assertTrue(is_file(app_path("Policies/$policy.php")));
        unlink(app_path("Policies/$policy.php"));
    }

    public function testMakingAPolicyWithAModelOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a policy with the model option
        $policy = "NewPolicy";
        $response = $this->artisan("make:policy", ["name" => $policy, "--model" => "NewModel"]);
        $response->expectsOutput("Policy created successfully.")->run();

        // I should have a policy in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Policies\\$policy"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Policies/$policy.php")));
    }

    public function testMakingAPolicyWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a policy
        $policy = "NewPolicy";
        $this->artisan("make:policy", ["name" => $policy]);

        // I should have a policy in my app dir
        $this->assertTrue(class_exists("App\\Policies\\$policy"));
        $this->assertTrue(is_file(app_path("Policies/$policy.php")));
        unlink(app_path("Policies/$policy.php"));
    }

    public function testMakingAPolicyWithoutModulesInitialised () : void
    {
        // If I make a policy
        $policy = "NewPolicy";
        $this->artisan("make:policy", ["name" => $policy]);

        // I should have a policy in my app dir
        $this->assertTrue(class_exists("App\\Policies\\$policy"));
        $this->assertTrue(is_file(app_path("Policies/$policy.php")));
        unlink(app_path("Policies/$policy.php"));
    }
}
