<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class ListenerMakeCommandTest extends ModuleTest
{
    public function testMakingAListenerWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a listener
        $listener = "NewListener";
        $response = $this->artisan("make:listener", ["name" => $listener]);
        $response->expectsOutput("Listener created successfully.");
        $response->run();

        // I should have a listener in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Listeners\\$listener"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Listeners/$listener.php")));
    }

    public function testMakingAListenerWithTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a listener with the module option
        $listener = "NewListener";
        $response = $this->artisan("make:listener", ["name" => $listener, "--module" => $module]);
        $response->expectsOutput("Listener created successfully.");
        $response->run();

        // I should have a listener in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Listeners\\$listener"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Listeners/$listener.php")));
    }

    public function testMakingAListenerWithEventOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a listener
        $listener = "NewListener";
        $response = $this->artisan("make:listener", ["name" => $listener, "--event" => "NewEvent"]);
        $response->expectsOutput("Listener created successfully.");
        $response->run();

        // I should have a listener in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Listeners\\$listener"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Listeners/$listener.php")));
    }

    public function testMakingAListenerWithQueuedOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a listener
        $listener = "NewListener";
        $response = $this->artisan("make:listener", ["name" => $listener, "--queued" => true]);
        $response->expectsOutput("Listener created successfully.");
        $response->run();

        // I should have a listener in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Listeners\\$listener"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Listeners/$listener.php")));
    }

    public function testMakingAListenerWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a listener
        $listener = "NewListener";
        $this->artisan("make:listener", ["name" => $listener]);

        // I should have a listener in my app dir
        $this->assertTrue(class_exists("App\\Listeners\\$listener"));
        $this->assertTrue(is_file(app_path("Listeners/$listener.php")));
        unlink(app_path("Listeners/$listener.php"));
    }

    public function testMakingAListenerWithoutModulesInitialised () : void
    {
        // If I make a listener
        $listener = "NewListener";
        $this->artisan("make:listener", ["name" => $listener]);

        // I should have a listener in my app dir
        $this->assertTrue(class_exists("App\\Listeners\\$listener"));
        $this->assertTrue(is_file(app_path("Listeners/$listener.php")));
        unlink(app_path("Listeners/$listener.php"));
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

        // And I make a listener with the module option
        $listener = "NewListener";
        $response = $this->artisan("make:listener", ["name" => $listener, "--module" => "vanilla"]);
        $response->expectsOutput("Listener created successfully.");
        $response->run();

        // I should have a listener in my app dir
        $this->assertTrue(class_exists("App\\Listeners\\$listener"));
        $this->assertTrue(is_file(app_path("Listeners/$listener.php")));
        unlink(app_path("Listeners/$listener.php"));
    }
}
