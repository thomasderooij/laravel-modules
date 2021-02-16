<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

use Thomasderooij\LaravelModules\Services\DependencyHandler;

class AddDependencyTest extends DependencyHandlerTest
{
    private $method = "addDependency";
    private $uut;
    private $methodHandler;

    protected function setUp(): void
    {
        parent::setUp();

        // We create a partial mock based on the dependency handler
        $mockableMethods = $this->getMockableClassMethods(DependencyHandler::class, $this->method);
        $string = implode(",", $mockableMethods);
        $this->methodHandler = \Mockery::mock(DependencyHandler::class . "[$string]", []);

        // And our method will be the unit under test
        $this->uut = $this->getMethodFromClass($this->method, DependencyHandler::class);
    }

    public function testAddDependency () : void
    {

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
