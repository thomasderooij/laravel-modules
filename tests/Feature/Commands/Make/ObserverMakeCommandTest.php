<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class ObserverMakeCommandTest extends ModuleTest
{
    public function testMakingAObserverWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a observer
        $observer = "NewObserver";
        $response = $this->artisan("make:observer", ["name" => $observer]);
        $response->expectsOutput("Observer created successfully.")->run();

        // I should have a observer in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Observers\\$observer"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Observers/$observer.php")));
    }

    public function testMakingAObserverWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a observer with the module option
        $observer = "NewObserver";
        $response = $this->artisan("make:observer", ["name" => $observer, "--module" => $module]);
        $response->expectsOutput("Observer created successfully.")->run();

        // I should have a observer in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Observers\\$observer"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Observers/$observer.php")));
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

        // And I make a observer with the module option
        $observer = "NewObserver";
        $response = $this->artisan("make:observer", ["name" => $observer, "--module" => "vanilla"]);
        $response->expectsOutput("Observer created successfully.")->run();

        // I should have a observer in my app dir
//        $this->assertTrue(class_exists("App\\Observers\\$observer"));
        $this->assertTrue(is_file(app_path("Observers/$observer.php")));
        unlink(app_path("Observers/$observer.php"));
    }

    public function testMakingAnObserverWithAModelOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make an observer with the model option
        $observer = "NewObserver";
        $model = "NewModel";
        $response = $this->artisan("make:observer", ["name" => $observer, "--model" => $model]);
        $response->expectsOutput("Observer created successfully.")->run();

        // I should have a observer in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Observers\\$observer"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Observers/$observer.php")));
    }

    public function testMakingAObserverWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a observer
        $observer = "NewObserver";
        $this->artisan("make:observer", ["name" => $observer]);

        // I should have a observer in my app dir
        $this->assertTrue(class_exists("App\\Observers\\$observer"));
        $this->assertTrue(is_file(app_path("Observers/$observer.php")));
        unlink(app_path("Observers/$observer.php"));
    }

    public function testMakingAPolicyWithoutModulesInitialised () : void
    {
        // If I make a observer
        $observer = "NewObserver";
        $this->artisan("make:observer", ["name" => $observer]);

        // I should have a observer in my app dir
        $this->assertTrue(class_exists("App\\Observers\\$observer"));
        $this->assertTrue(is_file(app_path("Observers/$observer.php")));
        unlink(app_path("Observers/$observer.php"));
    }
}
