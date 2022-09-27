<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\CircularReferenceException;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\DependencyAlreadyExistsException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class AddDependencyTest extends DependencyHandlerTest
{
    protected string $method = "addDependency";

    /**
     * Here is where we test adding a dependency in ideal conditions; i.e., its not upstream or downstream, and it's not already directly above us
     */
    public function testAddDependency(): void
    {
        // When I want to add a dependency between modules
        // I should fetch the contents of the tracker file
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn(
            $trackerContent = [
                "modules" => [
                    $this->upstreamModule,
                    $this->moduleInBetween,
                    $this->blueCollarModule,
                    $this->downstreamModule
                ],
                // modules needn't be active to set dependencies
                "activeModules" => [],
            ]
        )->once();
        // The modules should be checked and sanitised
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(true)->once();
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->upstreamModule])->andReturn(true)->once();
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->upstreamModule)]
        )->andReturn($this->upstreamModule)->once();
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->downstreamModule)]
        )->andReturn($this->downstreamModule)->once();
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($dependenciesKey = "dependencies")->once();

        // The dependency should be new
        $this->methodHandler->shouldReceive("dependencyExists")->withArgs(
            [$this->downstreamModule, $this->upstreamModule]
        )->andReturn(false)->once();

        // And the module should not create a circular reference
        $this->methodHandler->shouldReceive("wouldCreateCircularReference")->withArgs(
            [$this->downstreamModule, $this->upstreamModule]
        )->andReturn(false)->once();

        // The tracker content should be updated
        $update = $trackerContent;
        $update[$dependenciesKey][] = ["up" => $this->upstreamModule, "down" => $this->downstreamModule];

        // And then it should be saved
        $this->methodHandler->shouldReceive("save")->withArgs([$update])->once();

        // After the method is invoked
        $this->uut->invoke(
            $this->methodHandler,
            strtolower($this->downstreamModule),
            strtolower($this->upstreamModule)
        );
    }

    /**
     * Here we test what happens if we try to add a direct dependency when it already exists
     */
    public function testReAddingADirectDependency(): void
    {
        // When I want to add a dependency between modules
        // I should fetch the contents of the tracker file
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn(
            $trackerContent = [
                "modules" => [
                    $this->upstreamModule,
                    $this->moduleInBetween,
                    $this->blueCollarModule,
                    $this->downstreamModule
                ],
                "activeModules" => [],
                // but discover this dependency already exists
                "dependencies" => [
                    [$this->upKey => $this->upstreamModule, $this->downKey => $this->downstreamModule]
                ]
            ]
        );
        // The modules should be checked and sanitised
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->upstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->upstreamModule)]
        )->andReturn($this->upstreamModule);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->downstreamModule)]
        )->andReturn($this->downstreamModule);

        // The dependency should not be new
        $this->methodHandler->shouldReceive("dependencyExists")->withArgs(
            [$this->downstreamModule, $this->upstreamModule]
        )->andReturn(true);

        // And we expect an exception
        $this->expectException(DependencyAlreadyExistsException::class);
        $this->expectExceptionMessage(
            "module \"{$this->downstreamModule}\" is already dependent on \"{$this->upstreamModule}\"."
        );

        // After the method is invoked
        $this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upstreamModule);
    }

    public function testAddingCircularReference(): void
    {
        // When I want to add a dependency between modules
        // I should fetch the contents of the tracker file
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn(
            $trackerContent = [
                "modules" => [
                    $this->upstreamModule,
                    $this->moduleInBetween,
                    $this->blueCollarModule,
                    $this->downstreamModule
                ],
                "activeModules" => [],
                // And there is a dependency chain
                $dependenciesKey = "dependencies" => [
                    [$this->upKey => $this->upstreamModule, $this->downKey => $this->moduleInBetween],
                    [$this->upKey => $this->moduleInBetween, "$this->downKey" => $this->downstreamModule],
                ]
            ]
        );
        // The modules should be checked and sanitised
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->upstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->upstreamModule)]
        )->andReturn($this->upstreamModule);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->downstreamModule)]
        )->andReturn($this->downstreamModule);
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($dependenciesKey);

        // The dependency should be new
        $this->methodHandler->shouldReceive("dependencyExists")->withArgs(
            [$this->upstreamModule, $this->downstreamModule]
        )->andReturn(false);

        // But if it created a circle argument, it should throw an exception
        $this->methodHandler->shouldReceive("wouldCreateCircularReference")->withArgs(
            [$this->upstreamModule, $this->downstreamModule]
        )->andReturn(true);

        // There should be an exception
        $this->expectException(CircularReferenceException::class);
        $this->expectExceptionMessage(
            "module \"{$this->downstreamModule}\" is already upstream of \"{$this->upstreamModule}\"."
        );

        // If I try to close the circle
        $this->uut->invoke($this->methodHandler, $this->upstreamModule, $this->downstreamModule);
    }

    public function testAddingToANonExistingDownstreamModule(): void
    {
        // When I add a non existing module
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(false);

        // I should receive an exception
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("There is no module named \"{$this->downstreamModule}\".");

        $this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upstreamModule);
    }

    public function testAddingToANonExistingUpstreamModule(): void
    {
        // When I add a non existing module
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->upstreamModule])->andReturn(false);

        // I should receive an exception
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("There is no module named \"{$this->upstreamModule}\".");

        $this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upstreamModule);
    }
}
