<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class MiddlewareMakeCommandTest extends ModuleTest
{
    public function testMakingAMiddlewareWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a middleware
        $middleware = "NewMiddleware";
        $this->artisan("make:middleware", ["name" => $middleware]);

        // I should have a middleware in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Http\\Middleware\\$middleware"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Http/Middleware/$middleware.php")));
    }

    public function testMakingAMiddlewareWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a middleware with the module option
        $middleware = "NewMiddleware";
        $this->artisan("make:middleware", ["name" => $middleware, "--module" => $module]);

        // I should have a middleware in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Http\\Middleware\\$middleware"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Http/Middleware/$middleware.php")));
    }

    public function testMakingAMiddlewareWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a middleware
        $middleware = "NewMiddleware";
        $this->artisan("make:middleware", ["name" => $middleware]);

        // I should have a middleware in my app dir
        $this->assertTrue(class_exists("App\\Http\\Middleware\\$middleware"));
        $this->assertTrue(is_file(app_path("Http/Middleware/$middleware.php")));
        unlink(app_path("Http/Middleware/$middleware.php"));
    }

    public function testMakingAMiddlewareWithoutModulesInitialised () : void
    {
        // If I make a middleware
        $middleware = "NewMiddleware";
        $this->artisan("make:middleware", ["name" => $middleware]);

        // I should have a middleware in my app dir
        $this->assertTrue(class_exists("App\\Http\\Middleware\\$middleware"));
        $this->assertTrue(is_file(app_path("Http/Middleware/$middleware.php")));
        unlink(app_path("Http/Middleware/$middleware.php"));
    }
}
