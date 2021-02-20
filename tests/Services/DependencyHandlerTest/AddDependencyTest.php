<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\CircularReferenceException;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\DependencyAlreadyExistsException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;
use Thomasderooij\LaravelModules\Services\DependencyHandler;

class AddDependencyTest extends DependencyHandlerTest
{
    /**
     * The method we're testing
     *
     * @var string
     */
    private $method = "addDependency";

    /**
     * Our method, abstracted
     *
     * @var \ReflectionMethod
     */
    private $uut;

    private $moduleRoot = "MyModules";
    private $methodHandler;
    private $mockFilesystem;
    private $filesystem;

    private $upsteamModule = "master_module";
    private $moduleInBetween = "middle_manager";
    private $downstreamModule = "proletariat";
    private $blueCollarModule = "blue_collar";

    protected function setUp(): void
    {
        parent::setUp();

        // We create a partial mock based on the dependency handler
        $this->mockFilesystem = \Mockery::mock(Filesystem::class);
        $this->filesystem = $this->app->make(Filesystem::class);
        $mockableMethods = $this->getMockableClassMethods(DependencyHandler::class, $this->method);
        $string = implode(",", $mockableMethods);
        $this->methodHandler = \Mockery::mock(DependencyHandler::class . "[$string]", [
            $this->mockFilesystem
        ]);
        $this->methodHandler->shouldAllowMockingProtectedMethods();

        // And our method will be the unit under test
        $this->uut = $this->getMethodFromClass($this->method, DependencyHandler::class);
    }

    /**
     * Here is where we test adding a dependency in ideal conditions; i.e., its not upstream or downstream, and it's not already directly above us
     */
    public function testAddDependency () : void
    {
        // When I want to add a dependency between modules
        // I should fetch the contents of the tracker file
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn($trackerContent = [
            "modules" => [$this->upsteamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule],
            // modules needn't be active to set dependencies
            "activeModules" => [],
        ]);
        // The modules should be checked and sanitised
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->upsteamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->upsteamModule)])->andReturn($this->upsteamModule);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->downstreamModule)])->andReturn($this->downstreamModule);
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($dependenciesKey = "dependencies");

        // The dependency should be new
        $this->methodHandler->shouldReceive("dependencyExists")->withArgs([$this->downstreamModule, $this->upsteamModule])->andReturn(false);

        // And the module should not create a circular reference
        $this->methodHandler->shouldReceive("wouldCreateCircularReference")->withArgs([$this->downstreamModule, $this->upsteamModule])->andReturn(false);

        // The tracker content should be updated
        $update = $trackerContent;
        $update[$dependenciesKey][] = ["up" => $this->upsteamModule, "down" => $this->downstreamModule];

        // And then it should be saved
        $this->methodHandler->shouldReceive("save")->withArgs([$update]);

        // After the method is invoked
        $this->uut->invoke($this->methodHandler, strtolower($this->downstreamModule), strtolower($this->upsteamModule));
    }

    // todo: should this throw a dependency? probably.
    public function testAddingAnUpstreamDependency () : void
    {

    }

    /**
     * Here we test what happens if we try to add a direct dependency when it already exists
     */
    public function testReAddingADirectDependency () : void
    {
        // When I want to add a dependency between modules
        // I should fetch the contents of the tracker file
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn($trackerContent = [
            "modules" => [$this->upsteamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule],
            "activeModules" => [],
            // but discover this dependency already exists
            "dependencies" => [
                ["up" => $this->upsteamModule, "down" => $this->downstreamModule]
            ]
        ]);
        // The modules should be checked and sanitised
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->upsteamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->upsteamModule)])->andReturn($this->upsteamModule);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->downstreamModule)])->andReturn($this->downstreamModule);

        // The dependency should be new
        $this->methodHandler->shouldReceive("dependencyExists")->withArgs([$this->downstreamModule, $this->upsteamModule])->andReturn(true);

        // The tracker content should be updated
        $this->expectException(DependencyAlreadyExistsException::class);
        $this->expectExceptionMessage("module \"{$this->downstreamModule}\" is already dependent on \"{$this->upsteamModule}\".");

        // After the method is invoked
        $this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upsteamModule);
    }

    public function testAddingCircularReference () : void
    {
        // When I want to add a dependency between modules
        // I should fetch the contents of the tracker file
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn($trackerContent = [
            "modules" => [$this->upsteamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule],
            "activeModules" => [],
            // And there is a dependency chain
            $dependenciesKey = "dependencies" => [
                ["up" => $this->upsteamModule, "down" => $this->moduleInBetween],
                ["up" => $this->moduleInBetween, "down" => $this->downstreamModule],
            ]
        ]);
        // The modules should be checked and sanitised
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->upsteamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->upsteamModule)])->andReturn($this->upsteamModule);
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([strtolower($this->downstreamModule)])->andReturn($this->downstreamModule);
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($dependenciesKey);

        // The dependency should be new
        $this->methodHandler->shouldReceive("dependencyExists")->withArgs([$this->upsteamModule, $this->downstreamModule])->andReturn(false);

        // But if it created a circle argument, it should throw an exception
        $this->methodHandler->shouldReceive("wouldCreateCircularReference")->withArgs([$this->upsteamModule, $this->downstreamModule])->andReturn(true);

        // There should be an exception
        $this->expectException(CircularReferenceException::class);
        $this->expectExceptionMessage("module \"{$this->downstreamModule}\" is already upstream of \"{$this->upsteamModule}\".");

        // If I try to close the circle
        $this->uut->invoke($this->methodHandler, $this->upsteamModule, $this->downstreamModule);
    }

    public function testAddingToANonExistingDownstreamModule () : void
    {
        // When I add a non existing module
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(false);

        // I should receive an exception
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("There is no module named \"{$this->downstreamModule}\".");

        $this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upsteamModule);
    }

    public function testAddingToANonExistingUpstreamModule () : void
    {
        // When I add a non existing module
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->downstreamModule])->andReturn(true);
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$this->upsteamModule])->andReturn(false);

        // I should receive an exception
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("There is no module named \"{$this->upsteamModule}\".");

        $this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upsteamModule);
    }

    public function testAddingADepedencyToItself () : void
    {

    }
}
