<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

class WouldCreateCircularReferenceTest extends DependencyHandlerTest
{
    protected string $method = "wouldCreateCircularReference";

    /**
     * We only test the would, because the would not is already tested in the normal addDependencyTest
     */
    public function testWouldCreatedCircularReference () : void
    {
        // If I have a bunch of dependencies
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn($trackerContent = [
            "modules" => [$this->upstreamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule],
            "activeModules" => [],
            // And there is a dependency chain
            $key = "dependencies" => $dependencies = [
                [$this->upKey => $this->upstreamModule, $this->downKey => $this->downstreamModule],
            ]
        ]);
        // We should get the dependencies
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($key);
        // And we should have a look upstream
        $this->methodHandler->shouldReceive("getUpstreamModules")->withArgs([$this->downstreamModule, $dependencies])->andReturn($upstream = [
            // And find our upstream module, you guessed it, upstream
            $this->upstreamModule
        ]);

        // And I want to check if added our upstream module as downstream would create a conflict
        // Note: the first argument here should be upstream, and the second upstream, so I flipped them around
        $this->assertTrue($this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upstreamModule));
    }
}
