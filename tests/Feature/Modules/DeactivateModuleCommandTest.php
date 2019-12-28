<?php

namespace Tests\Feature\Modules;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Artisan;
use Thomasderooij\LaravelModules\Console\CompositeKernel;

class DeactivateModuleCommandTest extends ModuleTest
{
    public function testDeactivatingAModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And that module has a command
        $className = "TestCommand";
        $commandName = "test:command";
        $this->createCommand($className,$commandName);

        // And the module has a route
        $this->createRoute("get", "web", $module,"/test/route", "TestController@test", "testRoute");
        $routeCount = count($this->router->getRoutes()->getRoutes());

        // And I deactivate that module
        $response = $this->artisan("module:deactivate", ["name" => $module]);
        $response->expectsOutput("The module $module has been deactivated.");
        $response->run();

        // That command should no longer be available
        /** @var CompositeKernel $kernel */
        $kernel = $this->app->make(CompositeKernel::class);
        $this->assertArrayNotHasKey($commandName, $kernel->all());

        // And the module code should remain intact
        $this->assertFileExists(base_path(config("modules.root") . "/$module/Console/Commands/$className.php"));

        // And the module not be set to the workbench
        $this->assertNull($this->moduleManager->getWorkBench());

        // And the module should be set to inactive
        $this->assertFalse($this->moduleManager->moduleIsActive($module));

        // todo: add route checks to this.
//        $this->assertCount($routeCount - 1, $this->router->getRoutes()->getRoutes());
    }

    public function testCaseDoesNotMatter () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $this->createModule();

        // And I deactivate that module
        $response = $this->artisan("module:deactivate", ["name" => strtoupper($this->module)]);
        $response->expectsOutput("The module " .  strtoupper($this->module) . " has been deactivated.");
        $response->run();
    }

    public function testDeactivatingAModuleNotInMyWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);
        $this->createModule("OtherModule");

        // And that module has a command
        $className = "TestCommand";
        $commandName = "test:command";
        $this->createCommand($className,$commandName);

        // And I deactivate that module
        $this->artisan("module:deactivate", ["name" => $module]);

        // And the module should still be set to the workbench
        $this->assertSame("OtherModule", $this->moduleManager->getWorkBench());
    }

    public function testDeactivatingAnAlreadyDeactivatedModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And the module is already deactivated
        $this->moduleManager->deactivateModule($module);

        // And I deactivate that module
        $response = $this->artisan("module:deactivate", ["name" => $module]);
        $response->expectsOutput("The module $module is already deactivated.");
    }

    public function testDeactivatingANonExistentModule () : void
    {
        // If modules are not initiated
        // And I create a module names TestModule
        $response = $this->artisan("module:deactivate", ["name" => "TestModule"]);

        // I should get an error message
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
    }
}
