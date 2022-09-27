<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Mockery;
use Thomasderooij\LaravelModules\Services\DependencyHandler;

class DeleteDependencyModuleCommandTest extends CommandTest
{
    private $dependencyHandler;

    public function setUp(): void
    {
        parent::setUp();

        // Here we mock our dependency handler
        $this->dependencyHandler = Mockery::mock(DependencyHandler::class);
        $this->instance("module.service.dependency_handler", $this->dependencyHandler);

        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
    }

    /**
     * @group command
     */
    public function testDeletingAModuleDependency(): void
    {
        // If I want to delete a dependency from a module
        $response = $this->artisan("module:delete-dependency", ["name" => $module = "MyModule"]);

        // Things should pass our check
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module])->andReturn(true);

        // I should get a list of direct dependencies
        $this->dependencyHandler->shouldReceive("getAvailableModules")->withArgs([$module])->andReturnValues([
            $modules = [$module1 = "Module1", $module2 = "Module2", $module3 = "Module3", $module4 = "Module4"],
            [$module2, $module3, $module4],
            [$module2, $module4],
        ]);

        // I should be asked which of the dependencies I want to delete, and give a response
        $optionsInitial = $modules;
        array_unshift($optionsInitial, "None. I changed my mind");
        $response->expectsChoice("Which module do you want to remove from \"$module\"?", $module1, $optionsInitial);
        $this->dependencyHandler->shouldReceive("removeDependency")->withArgs([$module, $module1]);
        // I should be asked which other dependencies I want to delete, and respond with none
        $optionsAfter = [$noThnx = "No, I'm done removing dependencies", $module2, $module3, $module4];
        $response->expectsChoice(
            "Done. Would you like to remove another one from \"$module\"?",
            $module3,
            $optionsAfter
        );
        $this->dependencyHandler->shouldReceive("removeDependency")->withArgs([$module, $module3]);
        $optionsAfter2 = [$noThnx, $module2, $module4];
        $response->expectsChoice(
            "Done. Would you like to remove another one from \"$module\"?",
            $noThnx,
            $optionsAfter2
        );
        $this->dependencyHandler->shouldReceive("removeDependency")->withArgs([$noThnx]);

        // I should get a message saying the process is done and successful
        $response->expectsOutput("Alright. Your module dependencies have been updated.");

        $response->run();
    }
}
