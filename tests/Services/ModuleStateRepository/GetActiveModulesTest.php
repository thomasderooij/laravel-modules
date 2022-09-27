<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;

class GetActiveModulesTest extends ModuleStateRepositoryTest
{
    private $method = "getActiveModules";

    public function testGetActiveModules(): void
    {
        $uut = $this->getMethod($this->method);
        $repository = $this->getMockRepository($this->method);

        // If I have initialised my modules
        $repository->shouldReceive("isInitialised")->andReturn(true);

        // I will get my tracker content
        $modulesKey = "modules_key";
        $activeModulesKey = "active_modules_keys";
        $modules = ["module_1", "other_modules", "deprecated_module"];
        $activeModules = ["modules_1", "other_module"];
        $trackerContent = [$modulesKey => $modules, $activeModulesKey => $activeModules];
        $repository->expects("getTrackerContent")->twice()->andReturn($trackerContent);

        // And I will get the active modules out of that content
        $repository->expects("getActiveModulesTrackerKey")->twice()->andReturn($activeModulesKey);

        // When I get the active modules while using a check
        $this->assertSame($activeModules, $uut->invoke($repository, false));

        // I expect to check the tracker file if I'm not using the initialisation check
        $repository->expects("hasTrackerFile")->andReturn(true);

        $this->assertSame($activeModules, $uut->invoke($repository, true));
    }

    public function testGettingActiveModulesWhenModulesAreNotInitialised(): void
    {
        $uut = $this->getMethod($this->method);
        $repository = $this->getMockRepository($this->method);

        // If I not have initialised my modules
        $repository->shouldReceive("isInitialised")->andReturn(false);

        // And I do not have a tracker file
        $repository->shouldReceive("hasTrackerFile")->andReturn(false);

        // When I call for active modules while skipping the check
        $outcome = $uut->invoke($repository, true);

        // I expect an empty array
        $this->assertSame([], $outcome);
    }

    public function testGettingActiveModulesWhenNotInitialisedAndNotSkippingCheck(): void
    {
        $uut = $this->getMethod($this->method);
        $repository = $this->getMockRepository($this->method);

        // If I not have initialised my modules
        $repository->shouldReceive("isInitialised")->andReturn(false);

        // I expect an exception
        $this->expectException(ModulesNotInitialisedException::class);
        // With a message
        $this->expectExceptionMessage(
            "The modules need to be initialised first. You can do this by running the module:init command."
        );

        // When I call for active modules without skipping the check
        $outcome = $uut->invoke($repository, false);
    }
}
