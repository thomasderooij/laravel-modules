<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class ResourceMakeCommandTest extends ModuleTest
{
    public function testMakingAResourceWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a resource
        $resource = "NewResource";
        $response = $this->artisan("make:resource", ["name" => $resource]);
        $response->expectsOutput("Resource created successfully.");
        $response->run();

        // I should have a resource in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Http\\Resources\\$resource"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Http/Resources/$resource.php")));
    }

    public function testMakingAResourceWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a resource with the module option
        $resource = "NewResource";
        $response = $this->artisan("make:resource", ["name" => $resource, "--module" => $module]);
        $response->expectsOutput("Resource created successfully.");
        $response->run();

        // I should have a resource in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Http\\Resources\\$resource"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Http/Resources/$resource.php")));
    }

    public function testMakingAResourceWithACollectionOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a resource
        $resource = "NewResource";
        $response = $this->artisan("make:resource", ["name" => $resource, "--collection" => true]);
        $response->expectsOutput("Resource collection created successfully.");
        $response->run();

        // I should have a resource in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Http\\Resources\\$resource"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Http/Resources/$resource.php")));
    }

    public function testMakingAResourceWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a resource
        $resource = "NewResource";
        $this->artisan("make:resource", ["name" => $resource]);

        // I should have a resource in my app dir
        $this->assertTrue(class_exists("App\\Http\\Resources\\$resource"));
        $this->assertTrue(is_file(app_path("Http/Resources/$resource.php")));
        unlink(app_path("Http/Resources/$resource.php"));
    }

    public function testMakingAResourceWithoutModulesInitialised () : void
    {
        // If I make a resource
        $resource = "NewResource";
        $this->artisan("make:resource", ["name" => $resource]);

        // I should have a resource in my app dir
        $this->assertTrue(class_exists("App\\Http\\Resources\\$resource"));
        $this->assertTrue(is_file(app_path("Http/Resources/$resource.php")));
        unlink(app_path("Http/Resources/$resource.php"));
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

        // And I make a resource with the module option
        $resource = "NewResource";
        $response = $this->artisan("make:resource", ["name" => $resource, "--module" => "vanilla"]);
        $response->expectsOutput("Resource created successfully.");
        $response->run();

        // I should have a resource in my app dir
        $this->assertTrue(class_exists("App\\Http\\Resources\\$resource"));
        $this->assertTrue(is_file(app_path("Http/Resources/$resource.php")));
        unlink(app_path("Http/Resources/$resource.php"));
    }
}
