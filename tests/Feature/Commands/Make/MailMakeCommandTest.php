<?php

namespace Tests\Feature\Commands\Make;

use Tests\Feature\Modules\ModuleTest;

class MailMakeCommandTest extends ModuleTest
{
    public function testMakingAMailWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a mail
        $mail = "NewMail";
        $response = $this->artisan("make:mail", ["name" => $mail]);
        $response->expectsOutput("Mail created successfully.");
        $response->run();

        // I should have a mail in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Mail\\$mail"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Mail/$mail.php")));
    }

    public function testMakingAMailWithModuleTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a mail with the module option
        $mail = "NewMail";
        $response = $this->artisan("make:mail", ["name" => $mail, "--module" => $module]);
        $response->expectsOutput("Mail created successfully.");
        $response->run();

        // I should have a mail in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Mail\\$mail"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Mail/$mail.php")));
    }

    public function testMakingAMailWithForceOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And I make a mail
        $mail = "NewMail";
        $this->artisan("make:mail", ["name" => $mail]);

        // And I make the same mail again
        $response = $this->artisan("make:mail", ["name" => $mail, "--force" => true]);
        $response->expectsOutput("Mail created successfully.");
        $response->run();

        // I should have a mail in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Mail\\$mail"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Mail/$mail.php")));
    }

    public function testMakingAMailWithMarkdownOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And I make a mail
        $mail = "NewMail";
        $response = $this->artisan("make:mail", ["name" => $mail, "--markdown" => "mark.down"]);
        $response->expectsOutput("Mail created successfully.");
        $response->run();

        // I should have a mail in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Mail\\$mail"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Mail/$mail.php")));

        // And I should have a template in my resource folder
        $this->assertTrue(is_file(resource_path("views/mark/down.blade.php")));
        unlink(resource_path("views/mark/down.blade.php"));
    }

    public function testMakingAMailWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a mail
        $mail = "NewMail";
        $this->artisan("make:mail", ["name" => $mail]);

        // I should have a mail in my app dir
        $this->assertTrue(class_exists("App\\Mail\\$mail"));
        $this->assertTrue(is_file(app_path("Mail/$mail.php")));
        unlink(app_path("Mail/$mail.php"));
    }

    public function testMakingAMailWithoutModulesInitialised () : void
    {
        // If I make a mail
        $mail = "NewMail";
        $this->artisan("make:mail", ["name" => $mail]);

        // I should have a mail in my app dir
        $this->assertTrue(class_exists("App\\Mail\\$mail"));
        $this->assertTrue(is_file(app_path("Mail/$mail.php")));
        unlink(app_path("Mail/$mail.php"));
    }
}
