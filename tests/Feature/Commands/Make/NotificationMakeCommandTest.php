<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class NotificationMakeCommandTest extends ModuleTest
{
    public function testMakingANotificationWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a notification
        $notification = "NewNotification";
        $response = $this->artisan("make:notification", ["name" => $notification]);
        $response->expectsOutput("Notification created successfully.")->run();

        // I should have a notification in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Notifications\\$notification"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Notifications/$notification.php")));
    }

    public function testMakingANotificationWithModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a notification with the module option
        $notification = "NewNotification";
        $response = $this->artisan("make:notification", ["name" => $notification, "--module" => $module]);
        $response->expectsOutput("Notification created successfully.")->run();

        // I should have a notification in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Notifications\\$notification"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Notifications/$notification.php")));
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

        // And I make a notification with the module option
        $notification = "NewNotification";
        $response = $this->artisan("make:notification", ["name" => $notification, "--module" => "vanilla"]);
        $response->expectsOutput("Notification created successfully.")->run();

        // I should have a notification in my app dir
//        $this->assertTrue(class_exists("App\\Notifications\\$notification"));
        $this->assertTrue(is_file(app_path("Notifications/$notification.php")));
        unlink(app_path("Notifications/$notification.php"));
    }

    public function testMakingANotificationWithForceOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a notification
        $notification = "NewNotification";
        $this->artisan("make:notification", ["name" => $notification]);

        // And I make the notification again with the force option
        $response = $this->artisan("make:notification", ["name" => $notification, "--force" => true]);
        $response->expectsOutput("Notification created successfully.")->run();

        // I should have a notification in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Notifications\\$notification"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Notifications/$notification.php")));
    }

    public function testMakingANotificationWithMarkdownOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a notification with the markdown option
        $notification = "NewNotification";
        $response = $this->artisan("make:notification", ["name" => $notification, "--markdown" => "mark.down"]);
        $response->expectsOutput("Notification created successfully.")->run();

        // I should have a notification in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Notifications\\$notification"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Notifications/$notification.php")));

        // And I should have a template in my resource folder
        $this->assertTrue(is_file(resource_path("views/mark/down.blade.php")));
        unlink(resource_path("views/mark/down.blade.php"));
    }

    public function testMakingANotificationWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a notification
        $notification = "NewNotification";
        $this->artisan("make:notification", ["name" => $notification]);

        // I should have a notification in my app dir
        $this->assertTrue(class_exists("App\\Notifications\\$notification"));
        $this->assertTrue(is_file(app_path("Notifications/$notification.php")));
        unlink(app_path("Notifications/$notification.php"));
    }

    public function testMakingANotificationWithoutModulesInitialised () : void
    {
        // And I make a notification
        $notification = "NewNotification";
        $this->artisan("make:notification", ["name" => $notification]);

        // I should have a notification in my app dir
        $this->assertTrue(class_exists("App\\Notifications\\$notification"));
        $this->assertTrue(is_file(app_path("Notifications/$notification.php")));
        unlink(app_path("Notifications/$notification.php"));
    }
}
