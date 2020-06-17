<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class ChannelMakeCommandTest extends ModuleTest
{
    public function testMakingAChannelWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module in my workbench
        $module = "TestModule" ;
        $this->createModule($module);

        // And I make a channel
        $channel = "NewChannel";
        $this->artisan("make:channel", ["name" => $channel]);

        // I should have a channel in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Broadcasting\\$channel"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Broadcasting/$channel.php")));
    }

    public function testMakingAChannelWithTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a channel with the module option
        $channel = "NewChannel";
        $this->artisan("make:channel", ["name" => $channel, "--module" => $module]);

        // I should have a channel in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Broadcasting\\$channel"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Broadcasting/$channel.php")));
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

        // And I make a channel with the module option
        $channel = "NewChannel";
        $this->artisan("make:channel", ["name" => $channel, "--module" => "vanilla"]);

        // I should have a channel in my app dir
//        $this->assertTrue(class_exists("App\\Broadcasting\\$channel"));
        $this->assertTrue(is_file(app_path("Broadcasting/$channel.php")));
        unlink(app_path("Broadcasting/$channel.php"));
    }

    public function testMakingAChannelWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a channel
        $channel = "NewChannel";
        $this->artisan("make:channel", ["name" => $channel]);

        // I should have a channel in my app dir
        $this->assertTrue(class_exists("App\\Broadcasting\\$channel"));
        $this->assertTrue(is_file(app_path("Broadcasting/$channel.php")));
        unlink(app_path("Broadcasting/$channel.php"));
    }

    public function testMakingAChannelResourceWithoutModulesInitialised () : void
    {
        // If I make a channel
        $channel = "NewChannel";
        $this->artisan("make:channel", ["name" => $channel]);

        // I should have a channel in my app dir
        $this->assertTrue(class_exists("App\\Broadcasting\\$channel"));
        $this->assertTrue(is_file(app_path("Broadcasting/$channel.php")));
        unlink(app_path("Broadcasting/$channel.php"));
    }
}
