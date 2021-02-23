<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

class GetModulesFromStreamTest extends DependencyHandlerTest
{
    protected $method = "getModulesFromStream";

    public function testGettingModulesFromStream () : void
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
        $this->assertEquals([], $this->uut->invoke($this->methodHandler, $this->upstreamModule, $dependencies, true));
        $this->assertEquals([], $this->uut->invoke($this->methodHandler, $this->moduleInBetween, $dependencies, true));
        // And if I want to fetch the downstream
        $this->assertEquals([], $this->uut->invoke($this->methodHandler, $this->downstreamModule, $dependencies, false));

        // This is a bit of a workaround, but there is no assertion to check if array contents match
        $this->assertSame(
            $this->alphabeticalByValues([$this->upstreamModule, $this->moduleInBetween, $this->blueCollarModule]),
            $this->alphabeticalByValues($this->uut->invoke($this->methodHandler, $this->downstreamModule, $dependencies, true))
        );
        $this->assertSame(
            $this->alphabeticalByValues([$this->upstreamModule, $this->moduleInBetween]),
            $this->alphabeticalByValues($this->uut->invoke($this->methodHandler, $this->blueCollarModule, $dependencies, true))
        );

        $this->assertSame(
            $this->alphabeticalByValues([$this->blueCollarModule, $this->downstreamModule]),
            $this->alphabeticalByValues($this->uut->invoke($this->methodHandler, $this->upstreamModule, $dependencies, false))
        );
    }
}
