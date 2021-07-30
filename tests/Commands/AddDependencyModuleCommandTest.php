<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Mockery;
use Thomasderooij\LaravelModules\Services\DependencyHandler;

class AddDependencyModuleCommandTest extends CommandTest
{
    private $dependencyHandler;

    public function setUp(): void
    {
        parent::setUp();

        // Here we mock our dependency handler
        $this->dependencyHandler = Mockery::mock(DependencyHandler::class);
        $this->instance("module.service.dependency_handler", $this->dependencyHandler);
    }

    public function testAddingADependency () : void
    {
        // If I have a few modules
        $modules = [$auth = "Auth", $salesDomain = "SalesDomain", $adminDomain = "AdminDomain", $webshopDomain = "WebshopDomain"];

        // And I want to add a dependency to the sales domain
        $response = $this->artisan("module:add-dependency", ["name" => $salesDomain]);

        // The modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);

        // We fetch the workbench
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        // We should also have the module
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$salesDomain])->andReturn(true);

        $this->dependencyHandler->shouldReceive("getAvailableModules")->withArgs([$salesDomain])->andReturnValues([
            // first iteration (see expectsChoice below)
            [$auth, $adminDomain, $webshopDomain],
            // second iteration
            [$adminDomain, $webshopDomain],
        ])->twice();

        // I should get 3 options, consisting of the other 3 domains, and answer with Auth
        $doneMessage = "None. I'm done here.";
        $response->expectsChoice("Which module is \"$salesDomain\" dependent on?", $auth, [
            0 => $doneMessage,
            1 => $auth,
            2 => $adminDomain,
            3 => $webshopDomain,
        ]);

        // The dependencyHandler should then add the Auth module as a dependency
        $this->dependencyHandler->shouldReceive("addDependency")->withArgs([$salesDomain, $auth]);

        // Then I should get a follow-up question, consisting of the 2 remaining domains, and say that I'm done
        $response->expectsChoice("Alright. I've added it. What other module is \"$salesDomain\" dependent on?", $doneMessage, [
            0 => $doneMessage,
            1 => $adminDomain,
            2 => $webshopDomain,
        ]);

        // I should then get a response message confirming the process is done
        $response->expectsOutput("Roger that.");

        $response->run();
    }

    public function testAddingAllDependencies () : void
    {
        // If I have a few modules
        $modules = [$auth = "Auth", $salesDomain = "SalesDomain", $adminDomain = "AdminDomain", $webshopDomain = "WebshopDomain"];

        // And I want to add a dependency to the sales domain
        $response = $this->artisan("module:add-dependency", ["name" => $salesDomain]);

        // The modules should be initialised
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);

        // We fetch the workbench
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        // We should also have the module
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$salesDomain])->andReturn(true);

        $this->dependencyHandler->shouldReceive("getAvailableModules")->withArgs([$salesDomain])->andReturnValues([
            // first iteration (see expectsChoice below)
            [$auth],
            // second iteration
            [],
        ])->twice();

        // I should get 2 options, consisting of the other domain, and an option to terminate the process, and answer with Auth
        $doneMessage = "None. I'm done here.";
        $response->expectsChoice("Which module is \"$salesDomain\" dependent on?", $auth, [
            0 => $doneMessage,
            1 => $auth,
        ]);

        // The dependencyHandler should then add the Auth module as a dependency
        $this->dependencyHandler->shouldReceive("addDependency")->withArgs([$salesDomain, $auth]);

        // I should then get a response message confirming the process is done
        $response->expectsOutput("Roger that.");

        $response->run();
    }
}
