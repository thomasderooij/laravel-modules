<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class RequestMakeCommandTest extends ModuleTest
{
    public function testMakingARequestWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a request
        $request = "NewRequest";
        $this->artisan("make:request", ["name" => $request]);

        // I should have a request in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Http\\Requests\\$request"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Http/Requests/$request.php")));
    }

    public function testMakingARequestWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a request with the module option
        $request = "NewRequest";
        $this->artisan("make:request", ["name" => $request, "--module" => $module]);

        // I should have a request in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Http\\Requests\\$request"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Http/Requests/$request.php")));
    }

    public function testMakingARequestWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a request
        $request = "NewRequest";
        $this->artisan("make:request", ["name" => $request]);

        // I should have a request in my app dir
        $this->assertTrue(class_exists("App\\Http\\Requests\\$request"));
        $this->assertTrue(is_file(app_path("Http/Requests/$request.php")));
        unlink(app_path("Http/Requests/$request.php"));
    }

    public function testMakingARequestWithoutModulesInitialised () : void
    {
        // If I make a request
        $request = "NewRequest";
        $this->artisan("make:request", ["name" => $request]);

        // I should have a request in my app dir
        $this->assertTrue(class_exists("App\\Http\\Requests\\$request"));
        $this->assertTrue(is_file(app_path("Http/Requests/$request.php")));
        unlink(app_path("Http/Requests/$request.php"));
    }
}
