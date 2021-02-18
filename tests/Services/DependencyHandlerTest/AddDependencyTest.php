<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Services\DependencyHandler;

class AddDependencyTest extends DependencyHandlerTest
{
    private $method = "addDependency";
    private $uut;
    private $methodHandler;
    private $filesystem;

    /**
     * @group service
     */
    protected function setUp(): void
    {
        parent::setUp();

        // We create a partial mock based on the dependency handler
        $this->filesystem = \Mockery::mock(Filesystem::class);
        $mockableMethods = $this->getMockableClassMethods(DependencyHandler::class, $this->method);
        $string = implode(",", $mockableMethods);
        $this->methodHandler = \Mockery::mock(DependencyHandler::class . "[$string]", [
            $this->filesystem
        ]);

        // And our method will be the unit under test
        $this->uut = $this->getMethodFromClass($this->method, DependencyHandler::class);
    }

    public function testAddDependency () : void
    {
        // If I want to add a dependency between modules

//        $this->uut->invoke($this->methodHandler);
    }

    public function testAddingARedundantDependency () : void
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
