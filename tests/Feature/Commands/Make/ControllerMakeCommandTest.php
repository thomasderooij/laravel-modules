<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class ControllerMakeCommandTest extends ModuleTest
{
    public function testMakingAControllerWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $module = "TestModule" ;
        $this->createModule($module);

        // And I make a controller
        $controller = "NewController";
        $response = $this->artisan("make:controller", ["name" => $controller]);
        $response->expectsOutput("Controller created successfully.")->run();

        // I should have a controller in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Http\\Controllers\\$controller"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Http/Controllers/$controller.php")));
    }

    public function testMakingAControllerWithTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a controller with the module option
        $controller = "NewController";
        $response = $this->artisan("make:controller", ["name" => $controller, "--module" => $module]);
        $response->expectsOutput("Controller created successfully.")->run();

        // I should have a controller in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Http\\Controllers\\$controller"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Http/Controllers/$controller.php")));
    }

    public function testMakingAControllerWithAModelOptionWithTheModelNotPresent () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $this->createModule();

        // And I make a controller with a model
        $controller = "NewController";
        $model = "Aggregates\\NewModel";
        $expectedQuestion = "A " . $this->moduleManager->getModuleNameSpace($this->module) . $model . " model does not exist. Do you want to generate it?";
        $response = $this->artisan("make:controller", ["name" => $controller, "--model" => $model]);
        $response->expectsQuestion($expectedQuestion, "yes");
        $response->expectsOutput("Model created successfully.");
        $response->expectsOutput("Controller created successfully.");
        $response->run();

        // I should have a controller in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Http/Controllers/$controller.php")));

        // And I should have a model in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/" . str_replace("\\", "/", $model) . ".php")));
    }

    public function testMakingAControllerWithAResourceOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $this->createModule();

        // And I make a controller with the resource option
        $controller = "NewController";
        $response = $this->artisan("make:controller", ["name" => $controller, "--resource" => true]);
        $response->expectsOutput("Controller created successfully.")->run();

        // I should have a controller in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Http/Controllers/$controller.php")));
    }

    public function testMakingAControllerWithAInvokableOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $this->createModule();

        // And I make a controller with the invokable option
        $controller = "NewController";
        $response = $this->artisan("make:controller", ["name" => $controller, "--invokable" => true]);
        $response->expectsOutput("Controller created successfully.")->run();

        // I should have a controller in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Http/Controllers/$controller.php")));
    }

    public function testMakingAControllerWithAParentOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $this->createModule();

        // And I make a controller with the parent option
        $controller = "NewController";
        $model = "Aggregates\\NewModel";
        $expectedQuestion = "A " . $this->moduleManager->getModuleNameSpace($this->module) . $model . " model does not exist. Do you want to generate it?";
        $response = $this->artisan("make:controller", ["name" => $controller, "--parent" => $model]);
        $response->expectsQuestion($expectedQuestion, "yes");
        $response->expectsOutput("Model created successfully.");
        $response->expectsOutput("Controller created successfully.");
        $response->run();

        // I should have a controller in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Http/Controllers/$controller.php")));

        // And I should have a model in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/" . str_replace("\\", "/", $model) . ".php")));
    }

    public function testMakingAControllerWithAnApiOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $this->createModule();

        // And I make a controller with the api option
        $controller = "NewController";
        $response = $this->artisan("make:controller", ["name" => $controller, "--api" => true]);
        $response->expectsOutput("Controller created successfully.")->run();

        // I should have a controller in my module
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Http/Controllers/$controller.php")));
    }

    public function testMakingAControllerWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a controller
        $controller = "NewController";
        $this->artisan("make:controller", ["name" => $controller]);

        // I should have a controller in my app dir
        $this->assertTrue(class_exists("App\\Http\\Controllers\\$controller"));
        $this->assertTrue(is_file(app_path("Http/Controllers/$controller.php")));
        unlink(app_path("Http/Controllers/$controller.php"));
    }

    public function testMakingAControllerWithoutModulesInitialised () : void
    {
        // I make a controller
        $controller = "NewController";
        $this->artisan("make:controller", ["name" => $controller]);

        // I should have a controller in my app dir
        $this->assertTrue(class_exists("App\\Http\\Controllers\\$controller"));
        $this->assertTrue(is_file(app_path("Http/Controllers/$controller.php")));
        unlink(app_path("Http/Controllers/$controller.php"));
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

        // And I make a controller with the module option
        $controller = "NewController";
        $response = $this->artisan("make:controller", ["name" => $controller, "--module" => "vanilla"]);
        $response->expectsOutput("Controller created successfully.")->run();

        // I should have a controller in my app dir
        $this->assertTrue(class_exists("App\\Http\\Controllers\\$controller"));
        $this->assertTrue(is_file(app_path("Http/Controllers/$controller.php")));
        unlink(app_path("Http/Controllers/$controller.php"));
    }
}
