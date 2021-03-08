<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler;

class DependenciesModuleCommandTest extends CommandTest
{
    /**
     * @var DependencyHandler
     */
    private $dependencyHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dependencyHandler = $this->app->make("module.service.dependency_handler");
    }

    public function testListingDependenciesWithModuleArgument () : void
    {
        // If I have a bunch of modules
        $modules = [
            $topModule = "topModule",
            $secondPlace = "secondPlace",
            $sharedSecond = "sharedSecond",
            $upstream = "upstream",
            $module = "myModule",
            $downstream = "downstream",
            $lowerModule = "lowerModule",
            $bottomModule = "bottomModule",
            $otherModule = "otherModule",
            $anotherModule = "anotherModule",
        ];

        // And they're partially dependency on each other
        $this->dependencyHandler->addDependency($secondPlace, $topModule);
        $this->dependencyHandler->addDependency($sharedSecond, $topModule);
        $this->dependencyHandler->addDependency($module, $secondPlace);
        $this->dependencyHandler->addDependency($module, $sharedSecond);
        $this->dependencyHandler->addDependency($module, $upstream);
        $this->dependencyHandler->addDependency($downstream, $module);
        $this->dependencyHandler->addDependency($lowerModule, $module);
        $this->dependencyHandler->addDependency($bottomModule, $lowerModule);

        // And I ask for the dependencies
        $response = $this->artisan("module:dependencies");
        // I should get a list or modules, suffixed by "upstream", "current", "downstream" or "unrelated"

        $this->assertTrue(false);
    }

    public function testListingDependenciesWithWorkbench () : void
    {
        $this->assertTrue(false);
    }
}
