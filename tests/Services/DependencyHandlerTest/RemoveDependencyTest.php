<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

class RemoveDependencyTest extends DependencyHandlerTest
{
    protected string $method = "removeDependency";

    /**
     * @group service
     */
    public function testDeletingADependency(): void
    {
        // if I want to remove a dependency
        // I should fetch the contents of the tracker file
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn(
            $trackerContent = [
                "modules" => [
                    $this->upstreamModule,
                    $this->moduleInBetween,
                    $this->blueCollarModule,
                    $this->downstreamModule
                ],
                // modules needn't be active to set dependencies
                "activeModules" => [],
                $dependenciesKey = "dependencies" => [
                    ["up" => $upstream = "upstream", "down" => $downstream = "downstream"],
                ],
            ]
        )->once();

        // Fetch the dependencies key
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn("dependencies");

        // The upstream module has to exist
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$upstream])->andReturn(true)->once();
        // And the downstream has to exist
        $this->methodHandler->shouldReceive("hasModule")->withArgs([$downstream])->andReturn(true)->once();

        // Then we sanitise the module names
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([$upstream])->andReturn($upstream)->once();
        $this->methodHandler->shouldReceive("sanitiseModuleName")->withArgs([$downstream])->andReturn(
            $downstream
        )->once();

        // And we make sure the dependency exists
        $this->methodHandler->shouldReceive("dependencyExists")->withArgs([$downstream, $upstream])->andReturn(
            true
        )->once();

        // We update the tracker
        $trackerContent[$dependenciesKey] = [];
        // And we save the new content to the tracker file
        $this->methodHandler->shouldReceive("save")->withArgs([$trackerContent])->once();

        $this->uut->invoke($this->methodHandler, $downstream, $upstream);
    }
}
