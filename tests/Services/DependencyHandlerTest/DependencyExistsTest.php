<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

class DependencyExistsTest extends DependencyHandlerTest
{
    /**
     * The method we're testing
     *
     * @var string
     */
    protected string $method = "dependencyExists";

    public function testCheckingExistingMethod(): void
    {
        // If I want to know if a dependency exists, I should get the tracker content first
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn([
            "modules" => [
                $this->upstreamModule,
                $this->moduleInBetween,
                $this->blueCollarModule,
                $this->downstreamModule
            ],
            "activeModules" => [],
            $dependenciesKey = "dependencies" => [
                [$this->upKey => $this->upstreamModule, $this->downKey => $this->downstreamModule]
            ]
        ]);
        // Then the dependencies key should be fetched
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($dependenciesKey);

        // The method should return true if the dependency already exists
        $this->assertTrue($this->uut->invoke($this->methodHandler, $this->downstreamModule, $this->upstreamModule));
        // And false if it doesn't exist
        $this->assertFalse($this->uut->invoke($this->methodHandler, $this->upstreamModule, $this->downstreamModule));
        $this->assertFalse($this->uut->invoke($this->methodHandler, $this->moduleInBetween, $this->upstreamModule));
    }
}
