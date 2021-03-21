<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

class WouldCreateCircularReferenceTest extends DependencyHandlerTest
{
    protected string $method = "wouldCreateCircularReference";

    public function testWouldCreatedCircularReference () : void
    {
        // If I have a bunch of dependencies
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn($trackerContent = [
            "modules" => [$this->upstreamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule],
            "activeModules" => [],
            // And there is a dependency chain
            $key = "dependencies" => $dependencies = [
                [$this->upKey => $this->upstreamModule, $this->downKey => $this->moduleInBetween],
            ]
        ]);
        // We should get the dependencies
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($key);
        // And we should have a look downstream and find our upstream module
        $this->methodHandler->shouldReceive("getDownstreamModules")->withArgs([$this->moduleInBetween, $dependencies])->andReturn([$this->upstreamModule]);
        $this->methodHandler->shouldReceive("getDownstreamModules")->withArgs([$this->downstreamModule, $dependencies])->andReturn([]);

        // And I want to check if added our upstream module as downstream would create a conflict
        // Note: the first argument here should be upstream, and the second upstream, so I flipped them around
        $this->assertTrue($this->uut->invoke($this->methodHandler, $this->moduleInBetween, $this->upstreamModule));
        $this->assertFalse($this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upstreamModule));
    }
}
