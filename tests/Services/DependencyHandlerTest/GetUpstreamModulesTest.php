<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

class GetUpstreamModulesTest extends DependencyHandlerTest
{
    protected $method = "getUpstreamModules";

    public function testGettingUpstreamModules () : void
    {
        // If I have a bunch of dependencies
        // I should fetch the contents of the tracker file
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn($trackerContent = [
            "modules" => [$this->upstreamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule],
            "activeModules" => [],
            // And there is a dependency chain
            "dependencies" => $dependencies = [
                [$this->upKey => $this->upstreamModule, $this->downKey => $this->downstreamModule],
                [$this->upKey => $this->moduleInBetween, $this->downKey => $this->downstreamModule],
                [$this->upKey => $this->upstreamModule, $this->downKey => $this->blueCollarModule],
                [$this->upKey => $this->blueCollarModule, $this->downKey => $this->downstreamModule],
                [$this->upKey => $this->moduleInBetween, $this->downKey => $this->blueCollarModule]
            ]
        ]);

        // And I want to fetch the upstream
        // It should give me all others for the downstream module
        $this->assertEquals([], $this->uut->invoke($this->methodHandler, $this->upstreamModule, $dependencies));
        $this->assertSame([], $this->uut->invoke($this->methodHandler, $this->moduleInBetween, $dependencies));

        // This is a bit of a workaround, but there is no assertion to check if array contents match
        $this->assertSame(
            collect([$this->upstreamModule, $this->moduleInBetween, $this->blueCollarModule])->sort()->values()->toArray(),
            collect($this->uut->invoke($this->methodHandler, $this->downstreamModule, $dependencies))->sort()->values()->toArray()
        );
        $this->assertSame(
            collect([$this->upstreamModule, $this->moduleInBetween])->sort()->values()->toArray(),
            collect($this->uut->invoke($this->methodHandler, $this->blueCollarModule, $dependencies))->sort()->values()->toArray()
        );
    }
}
