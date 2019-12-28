<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class EventMakeCommandTest extends ModuleTest
{
    public function testMakingAnEventWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make an event
        $event = "NewEvent";
        $this->artisan("make:event", ["name" => $event]);

        // I should have a event in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Events\\$event"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Events/$event.php")));
    }

    public function testMakingAnEventWithTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make an event with the module option
        $event = "NewEvent";
        $this->artisan("make:event", ["name" => $event, "--module" => $module]);

        // I should have a event in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Events\\$event"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Events/$event.php")));
    }

    public function testMakingAnEventWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a event
        $event = "NewEvent";
        $this->artisan("make:event", ["name" => $event]);

        // I should have a event in my app dir
        $this->assertTrue(class_exists("App\\Events\\$event"));
        $this->assertTrue(is_file(app_path("Events/$event.php")));
        unlink(app_path("Events/$event.php"));
    }

    public function testMakingAnEventWithoutModulesInitialised () : void
    {
        // If I make a event
        $event = "NewEvent";
        $this->artisan("make:event", ["name" => $event]);

        // I should have a event in my app dir
        $this->assertTrue(class_exists("App\\Events\\$event"));
        $this->assertTrue(is_file(app_path("Events/$event.php")));
        unlink(app_path("Events/$event.php"));
    }
}
