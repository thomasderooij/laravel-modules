<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

class GetAvailableModulesTest extends DependencyHandlerTest
{
    protected $method = "getAvailableModules";

    private $unrelatedModule = "unrelated_module";
    private $unrelatedModule2 = "unrelatedModule";

    public function testGettingAvailableModulesForUpstream () : void
    {
        // If I want to add a dependency between modules
        // And I have a bunch of modules
        $this->setTrackerContentExpectation();

        // We should get all the modules
        $this->setGetModulesExpectation();
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn("dependencies");
        $this->methodHandler->shouldReceive("getDownstreamModules")->andReturn([$this->downstreamModule, $this->blueCollarModule]);

        // There are a few things we don't want to see:
        //  - Modules that are downstream
        //  - Modules that are directly upstream
        //  - The module we're asking to see the options for
        $expected = $this->alphabeticalByValues([$this->moduleInBetween, $this->unrelatedModule, $this->unrelatedModule2]);
        $this->assertSame($expected, $this->alphabeticalByValues($this->uut->invoke($this->methodHandler, $this->upstreamModule)));
    }

    public function testGettingAvailableModulesForModuleInBetween () : void
    {
        $this->setExpectations();
        $this->methodHandler->shouldReceive("getDownstreamModules")->andReturn([$this->downstreamModule, $this->blueCollarModule]);

        $expected = $this->alphabeticalByValues([$this->upstreamModule, $this->unrelatedModule, $this->unrelatedModule2]);
        $this->assertSame($expected, $this->alphabeticalByValues($this->uut->invoke($this->methodHandler, $this->moduleInBetween)));
    }

    public function testGettingAvaiableModulesForBlueCollar () : void
    {
        $this->setExpectations();
        $this->methodHandler->shouldReceive("getDownstreamModules")->andReturn([$this->downstreamModule]);

        $expected = $this->alphabeticalByValues([$this->unrelatedModule, $this->unrelatedModule2]);
        $this->assertSame($expected, $this->alphabeticalByValues($this->uut->invoke($this->methodHandler, $this->blueCollarModule)));
    }

    public function testGettingAvaiableModulesForDownstream () : void
    {
        $this->setExpectations();
        $this->methodHandler->shouldReceive("getDownstreamModules")->andReturn([]);

        $expected = $this->alphabeticalByValues([$this->unrelatedModule, $this->unrelatedModule2]);
        $this->assertSame($expected, $this->alphabeticalByValues($this->uut->invoke($this->methodHandler, $this->downstreamModule)));
    }

    public function testGettingAvailableModulesForUnrelatedModule () : void
    {
        $this->setExpectations();
        $this->methodHandler->shouldReceive("getDownstreamModules")->andReturn([]);

        $expected = $this->alphabeticalByValues([$this->upstreamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule, $this->unrelatedModule2]);
        $this->assertSame($expected, $this->alphabeticalByValues($this->uut->invoke($this->methodHandler, $this->unrelatedModule)));
    }

    private function setExpectations () : void
    {
        $this->setTrackerContentExpectation();
        $this->setGetModulesExpectation();
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn("dependencies");
    }

    private function setTrackerContentExpectation () : void
    {
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn($trackerContent = [
            "modules" => $modules = [
                // Modules with dependencies
                $this->upstreamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule,
                // Modules without dependencies
                $unrelatedModule = "unrelated_module", $unrelatedModule2 = "unrelatedModule"
            ],
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
    }

    private function setGetModulesExpectation () : void
    {
        $this->methodHandler->shouldReceive("getModules")->andReturn([
            // Modules with dependencies
            $this->upstreamModule, $this->moduleInBetween, $this->blueCollarModule, $this->downstreamModule,
            // Modules without dependencies
            $unrelatedModule = "unrelated_module", $unrelatedModule2 = "unrelatedModule"
        ]);
    }
}
