<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewModuleCommandTest extends ModuleTest
{
    public function testNewModuleCreation () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a new module
        $module = "TestModule";
        $response = $this->artisan("module:new", ["name" => $module]);
        $response->expectsOutput("Your module has been created in the " . config("modules.root") . "/$module directory.");
        $response->run();

        // I should have a command kernel in the root directory
        $this->assertTrue(is_file(base_path("{$this->root}/$module/Console/Kernel.php")));

        // I should have routing files
        $this->assertTrue(is_file(base_path("{$this->root}/$module/routes/web.php")));
        $this->assertTrue(is_file(base_path("{$this->root}/$module/routes/api.php")));
        $this->assertTrue(is_file(base_path("{$this->root}/$module/routes/console.php")));

        // I should have service provider files
        $this->assertTrue(is_file(base_path("{$this->root}/$module/Providers/AuthServiceProvider.php")));
        $this->assertTrue(is_file(base_path("{$this->root}/$module/Providers/BroadcastServiceProvider.php")));
        $this->assertTrue(is_file(base_path("{$this->root}/$module/Providers/EventServiceProvider.php")));
        $this->assertTrue(is_file(base_path("{$this->root}/$module/Providers/RouteServiceProvider.php")));

        // I should have a base controller
        $this->assertTrue(is_file(base_path("{$this->root}/$module/Http/Controllers/Controller.php")));

        // The module should be set to active
        $this->assertTrue($this->moduleManager->moduleIsActive(strtolower($module)));

        // The module should be in my workbench
        $this->assertSame($module, $this->moduleManager->getWorkBench());
    }

    public function testIShouldBeAbleToCreateMultipleModules () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a new module
        $module = "TestModule";
        $this->createModule($module);

        // And I create another module
        $otherModule = "OtherModule";
        $response = $this->artisan("module:new", ["name" => "OtherModule"]);
        $response->expectsOutput("Your module has been created in the " . config("modules.root") . "/$otherModule directory.");
        $response->run();

        // There should be two modules
        $this->assertTrue(is_dir(base_path("{$this->root}/$otherModule")));
        $this->assertTrue(is_dir(base_path("{$this->root}/$module")));
        $this->assertTrue($this->moduleManager->hasModule($module));
        $this->assertTrue($this->moduleManager->hasModule($otherModule));
    }

    public function testIShouldNotBeAbleToCreateTheSameModuleMoreThanOnce () : void
    {
        // If modules are initiated
        $this->initModules();

        // And there is a module initiated
        $module = "TestModule";
        $this->artisan("module:new", ["name" => $module]);

        // And I create a module names TestModule
        $response = $this->artisan("module:new", ["name" => $module]);

        $response->expectsOutput("Module $module already exists.");
    }

    public function testIShouldNotBeAbleToCreateAModuleIfThePackageIsNotInitiated () : void
    {
        // If modules are not initiated
        // And I create a module names TestModule
        $response = $this->artisan("module:new", ["name" => "TestModule"]);

        // I should get an error message
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
    }
}
