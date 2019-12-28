<?php

namespace Tests\Feature\Modules;

use Thomasderooij\LaravelModules\CompositeProviders\RouteCompositeServiceProvider;
use Thomasderooij\LaravelModules\Console\CompositeKernel;

class ActivateModuleCommandTest extends ModuleTest
{
//    public function testActivatingAModuleWithAnEmptyWorkbench () : void
//    {
//        // If I initiate modules
//        $this->initModules();
//
//        // And I have a module
//        $this->createModule();
//
//        // And that module has a command
//        $className = "TestCommand";
//        $commandName = "test:command";
//        $this->createCommand($className,$commandName);
//
//        // And the module has a route
//        $this->createRoute("get", "web", $this->module,"/test/route", "TestController@test", "testRoute");
//
//        // And the module is not active
//        $this->moduleManager->deactivateModule($this->module);
//        $routeCount = count($this->router->getRoutes()->getRoutes());
//
//        // And my workbench is empty
//        $this->moduleManager->clearWorkbench();
//
//        // And I activate that module
//        $response = $this->artisan("module:activate", ["name" => $this->module]);
//        $response->expectsOutput("The module {$this->module} has been activated.");
//        $response->run();
//
//        // That command should be available
//        /** @var CompositeKernel $kernel */
//        $kernel = $this->app->make(CompositeKernel::class);
//        $this->assertArrayHasKey($commandName, $kernel->all());
//
//        // And the module code should remain intact
//        $this->assertFileExists(base_path(config("modules.root") . "/{$this->module}/Console/Commands/$className.php"));
//
//        // And the module be set to the workbench
//        $this->assertSame($this->moduleManager->getWorkBench(), $this->module);
//
//        // And the module should be set to active
//        $this->assertTrue($this->moduleManager->moduleIsActive($this->module));
//
////         todo: rebuild the service providers to register this route
////        $this->assertCount($routeCount + 1, $this->router->getRoutes()->getRoutes());
//    }

    public function testCaseDoesNotMatter () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module
        $this->createModule();

        // And the module is not active
        $this->moduleManager->deactivateModule($this->module);

        $response = $this->artisan("module:activate", ["name" => strtoupper($this->module)]);
        $response->expectsOutput("The module " . strtoupper($this->module) . " has been activated.");
        $response->run();
    }

    public function testActivatingAModuleWithAFilledWorkbench () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have two modules
        $module = "TestModule";
        $this->createModule($module);
        $other = "OtherModule";
        $this->createModule($other);

        // And that module has a command
        $className = "TestCommand";
        $commandName = "test:command";
        $this->createCommand($className,$commandName);

        // And the module is not active
        $this->moduleManager->deactivateModule($module);

        // And my workbench has the other module in it
        $this->moduleManager->setWorkbench($other);

        // And I acactivate that module
        $this->artisan("module:activate", ["name" => $module]);

        // I should still have the other module in my workbench
        $this->assertSame($this->moduleManager->getWorkBench(), $other);
    }

    public function testActivatingAnAlreadyActiveModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module thats active
        $module = "TestModule";
        $this->createModule($module);

        // And that module has a command
        $className = "TestCommand";
        $commandName = "test:command";
        $this->createCommand($className,$commandName);

        // And I activate that module
        $response = $this->artisan("module:activate", ["name" => $module]);
        $response->expectsOutput("The module $module is already active.");
        $response->run();
    }

    public function testActivatingANonExistentModule () : void
    {
        // If I initiate modules
        $this->initModules();

        // And I have a module thats active
        $module = "TestModule";
        $this->createModule($module);

        // And that module has a command
        $className = "TestCommand";
        $commandName = "test:command";
        $this->createCommand($className,$commandName);

        // And I activate that module
        $response = $this->artisan("module:activate", ["name" => "WrongModule"]);
        $response->expectsOutput("There is no module named WrongModule.");
    }

    public function testActivatingAModuleWhenModulesAreNotInitialised () : void
    {
        // If my modules are not initialised
        // And I activate a module
        $response = $this->artisan("module:activate", ["name" => "TestModule"]);
        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
    }
}
