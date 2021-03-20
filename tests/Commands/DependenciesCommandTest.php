<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Mockery;
use Mockery\MockInterface;
use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class DependenciesCommandTest extends Test
{
    /**
     * @var ModuleManager|MockInterface
     */
    private $moduleManager;

    /**
     * @var DependencyHandler|MockInterface
     */
    private $dependencyHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager = Mockery::mock(\Thomasderooij\LaravelModules\Services\ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);

        $this->dependencyHandler = Mockery::mock(\Thomasderooij\LaravelModules\Services\DependencyHandler::class);
        $this->app->instance("module.service.dependency_handler", $this->dependencyHandler);
    }

    public function testListingDependenciesWithModuleArgument () : void
    {
        // If I have a bunch of modules
        $modules = [
            $topModule = "topModule",
            $secondPlace = "secondPlace",
            $sharedSecond = "sharedSecond",
            $upstream = "upstream",
            $module = "myModule",
            $downstream = "downstream",
            $lowerModule = "lowerModule",
            $bottomModule = "bottomModule",
            $otherModule = "otherModule",
            $anotherModule = "anotherModule",
        ];

        // And they're partially dependency on each other
        $this->dependencyHandler->shouldReceive("getUpstreamModules")->withArgs([$module])->andReturn([
            $topModule, $secondPlace, $sharedSecond, $upstream
        ]);
        $this->dependencyHandler->shouldReceive("getDownstreamModules")->withArgs([$module])->andReturn([
            $downstream, $lowerModule, $bottomModule
        ]);

        // The module manager should confirm that the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn($modules);
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);

        // And I ask for the dependencies
        $response = $this->artisan("module:dependencies", ["--module" => $module]);

        // I should get a list or modules, suffixed by "upstream", "current", "downstream" or "unrelated"
        $response->expectsOutput("$topModule (upstream)");
        $response->expectsOutput("$secondPlace (upstream)");
        $response->expectsOutput("$sharedSecond (upstream)");
        $response->expectsOutput("$upstream (upstream)");
        $response->expectsOutput("$module (current)");
        $response->expectsOutput("$downstream (downstream)");
        $response->expectsOutput("$lowerModule (downstream)");
        $response->expectsOutput("$bottomModule (downstream)");
        $response->expectsOutput("$otherModule (unrelated)");
        $response->expectsOutput("$anotherModule (unrelated)");

        // When I execute the command
        $response->run();
    }

    public function testListingDependenciesWithWorkbench () : void
    {
        // If I have a bunch of modules
        $modules = [
            $topModule = "topModule",
            $secondPlace = "secondPlace",
            $sharedSecond = "sharedSecond",
            $upstream = "upstream",
            $module = "myModule",
            $downstream = "downstream",
            $lowerModule = "lowerModule",
            $bottomModule = "bottomModule",
            $otherModule = "otherModule",
            $anotherModule = "anotherModule",
        ];

        // The module manager should confirm that the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn($module);
        $this->moduleManager->shouldReceive("getActiveModules")->andReturn($modules);
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);

        // And they're partially dependency on each other
        $this->dependencyHandler->shouldReceive("getUpstreamModules")->withArgs([$module])->andReturn([
            $topModule, $secondPlace, $sharedSecond, $upstream
        ]);
        $this->dependencyHandler->shouldReceive("getDownstreamModules")->withArgs([$module])->andReturn([
            $downstream, $lowerModule, $bottomModule
        ]);

        // And I ask for the dependencies
        $response = $this->artisan("module:dependencies");

        // I should get a list or modules, suffixed by "upstream", "current", "downstream" or "unrelated"
        $response->expectsOutput("$topModule (upstream)");
        $response->expectsOutput("$secondPlace (upstream)");
        $response->expectsOutput("$sharedSecond (upstream)");
        $response->expectsOutput("$upstream (upstream)");
        $response->expectsOutput("$module (current)");
        $response->expectsOutput("$downstream (downstream)");
        $response->expectsOutput("$lowerModule (downstream)");
        $response->expectsOutput("$bottomModule (downstream)");
        $response->expectsOutput("$otherModule (unrelated)");
        $response->expectsOutput("$anotherModule (unrelated)");

        // When I execute the command
        $response->run();
    }

    public function testListingModulesWithoutModuleArgumentAndWithoutWorkbench () : void
    {
        // If I have a bunch of modules
        // The module manager should confirm that the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // And that the workbench is empty
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        // And I ask for the dependencies
        $response = $this->artisan("module:dependencies");

        // I should get a notification that no module was provided
        $response->expectsOutput("No module option was provided, nor was a module found in your workbench.");

        // When I execute the command
        $response->run();
    }

    public function testListingModulesWithNonExistingModule () : void
    {
        // If I have a bunch of modules
        // The module manager should confirm that the modules are initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        // And that the workbench is empty
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module = "I'm no good"])->andReturn(false);

        // And I ask for the dependencies for a non-existent module
        $response = $this->artisan("module:dependencies", ["--module" => $module]);

        // I expect to get a notification that the module does not exist
        $response->expectsOutput("There is no module named \"$module\".");

        // When I ask for its dependencies
        $response->run();
    }
}
