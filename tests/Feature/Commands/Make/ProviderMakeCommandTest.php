<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class ProviderMakeCommandTest extends ModuleTest
{
    public function testMakingAProviderWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a provider
        $provider = "NewProvider";
        $this->artisan("make:provider", ["name" => $provider]);

        // I should have a provider in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Providers\\$provider"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Providers/$provider.php")));
    }

    public function testMakingAProviderWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a provider with the module option
        $provider = "NewProvider";
        $this->artisan("make:provider", ["name" => $provider, "--module" => $module]);

        // I should have a provider in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Providers\\$provider"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Providers/$provider.php")));
    }

    public function testMakingAProviderWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a provider
        $provider = "NewProvider";
        $this->artisan("make:provider", ["name" => $provider]);

        // I should have a provider in my app dir
        $this->assertTrue(class_exists("App\\Providers\\$provider"));
        $this->assertTrue(is_file(app_path("Providers/$provider.php")));
        unlink(app_path("Providers/$provider.php"));
    }

    public function testMakingAProviderWithoutModulesInitialised () : void
    {
        // If I make a provider
        $provider = "NewProvider";
        $this->artisan("make:provider", ["name" => $provider]);

        // I should have a provider in my app dir
        $this->assertTrue(class_exists("App\\Providers\\$provider"));
        $this->assertTrue(is_file(app_path("Providers/$provider.php")));
        unlink(app_path("Providers/$provider.php"));
    }
}
