<?php

namespace TestsDeprecated\Feature\Commands\Make;

use TestsDeprecated\Feature\Modules\ModuleTest;

class JobMakeCommandTest extends ModuleTest
{
    public function testMakingAJobWithModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $module = "TestModule" ;
        $this->createModule($module);

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a job
        $job = "NewJob";
        $response = $this->artisan("make:job", ["name" => $job]);
        $response->expectsOutput("Job created successfully.");
        $response->run();

        // I should have a job in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Jobs\\$job"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Jobs/$job.php")));
    }

    public function testMakingAJobWithTheModuleOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules, of which the latter is in my workbench
        $module = "TestModule" ;
        $this->createModule($module);
        $otherModule = "OtherModule";
        $this->createModule($otherModule);

        // And I make a job with the module option
        $job = "NewJob";
        $response = $this->artisan("make:job", ["name" => $job, "--module" => $module]);
        $response->expectsOutput("Job created successfully.");
        $response->run();

        // I should have a job in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "Jobs\\$job"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/$module/Jobs/$job.php")));
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

        // And I make a job with the module option
        $job = "NewJob";
        $response = $this->artisan("make:job", ["name" => $job, "--module" => "vanilla"]);
        $response->expectsOutput("Job created successfully.");
        $response->run();

        // I should have a job in my app dir
//        $this->assertTrue(class_exists("App\\Jobs\\$job"));
        $this->assertTrue(is_file(app_path("Jobs/$job.php")));
        unlink(app_path("Jobs/$job.php"));
    }

    public function testMakingAJobWithSyncOption () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And my module is set to my workbench
        $this->moduleManager->setWorkbench($this->module);

        // And I make a job
        $job = "NewJob";
        $response = $this->artisan("make:job", ["name" => $job, "--sync" => true]);
        $response->expectsOutput("Job created successfully.");
        $response->run();

        // I should have a job in my module
//        $this->assertTrue(class_exists($this->moduleManager->getModuleNameSpace($module) . "\\Jobs\\$job"));
        $this->assertTrue(is_file(base_path(config("modules.root") . "/{$this->module}/Jobs/$job.php")));
    }

    public function testMakingAJobWithoutModuleInWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And workbench is empty
        $this->moduleManager->clearWorkbench();

        // And I make a job
        $job = "NewJob";
        $this->artisan("make:job", ["name" => $job]);

        // I should have a job in my app dir
        $this->assertTrue(class_exists("App\\Jobs\\$job"));
        $this->assertTrue(is_file(app_path("Jobs/$job.php")));
        unlink(app_path("Jobs/$job.php"));
    }

    public function testMakingAJobWithoutModulesInitialised () : void
    {
        // If I make a job
        $job = "NewJob";
        $this->artisan("make:job", ["name" => $job]);

        // I should have a job in my app dir
        $this->assertTrue(class_exists("App\\Jobs\\$job"));
        $this->assertTrue(is_file(app_path("Jobs/$job.php")));
        unlink(app_path("Jobs/$job.php"));
    }
}
