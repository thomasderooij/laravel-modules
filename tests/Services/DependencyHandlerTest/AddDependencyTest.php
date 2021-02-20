<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\DependencyAlreadyExistsException;
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

        // The tracker content should be updated
        $update = $trackerContent;
        $update["dependencies"][] = ["up" => $this->upsteamModule, "down" => $this->downstreamModule];

        // And then it should be saved
        $this->methodHandler->shouldReceive("save")->withArgs([$update]);

        // After the method is invoked
        $this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upsteamModule);
    }

    // todo: should this throw a dependency? probably. but go for the command test first
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

        // The tracker content should be updated
        $this->expectException(DependencyAlreadyExistsException::class);
        $this->expectExceptionMessage("module \"{$this->downstreamModule}\" is already dependent on \"{$this->upsteamModule}\".");

        // After the method is invoked
        $this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upsteamModule);
    }

    public function testAddingCircularReference () : void
    {

    }

    public function testAddingToANonExistingModule () : void
    {

    }

    public function testAddingANonExistingModule () : void
    {

    }

    public function testAddingADownstreamModule () : void
    {

    }
}
