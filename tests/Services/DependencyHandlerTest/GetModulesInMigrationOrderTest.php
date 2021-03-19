<?php

declare(strict_types=1);

namespace DependencyHandlerTest;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest\DependencyHandlerTest;

class GetModulesInMigrationOrderTest extends DependencyHandlerTest
{
    protected string $method = "getModulesInMigrationOrder";

    public function testGetMigrationOrderWhenNoDependenciesHaveBeenSpecified () : void
    {
        // If I ask for the modules in migration order
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn([]);
        // And there is no dependencies key specified
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn("dependencies");

        Config::shouldReceive("get")->withArgs(["modules.vanilla", null])->andReturn($vanilla = "Vanilla");

        // I should get all active modules
        $this->methodHandler->shouldReceive("getActiveModules")->andReturn([
            $module1 = "module_1",
            $module2 = "module_2",
        ]);

        // And return a array containing vanilla, and the active modules
        $expected = [$vanilla, $module1, $module2];
        $this->assertSame($expected, $this->uut->invoke($this->methodHandler));
    }

    /**
     * @group uut
     */
    public function testGetMigrationOrderWhenSomeDependenciesHaveBeenSpecified () : void
    {
        // If I ask for the modules in migration order
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn([
            $dependenciesKey = "dependenciesKey" => [
                ["up" => $topModule = "topModule", "down" => $layer1Module = "layer1Module"],
                ["up" => $topModule2 = "topModule2", "down" => $layer1Module],
                ["up" => $layer1Module, "down" => $layer2Module = "layer2Module"],
                ["up" => $layer1Module, "down" => $layer2Module2 = "layer2Module2"],
            ],
        ]);
        // And there is no dependencies key specified
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($dependenciesKey);

        Config::shouldReceive("get")->withArgs(["modules.vanilla", null])->andReturn($vanilla = "Vanilla");

        // I should get all active modules
        $this->methodHandler->shouldReceive("getActiveModules")->andReturn([
            $topModule, $module1 = "module_1", $layer2Module, $layer2Module2,
            $module2 = "module_2", $topModule2, $layer1Module,
        ]);

        // And return a array containing vanilla, and the active modules
        $expected = [$vanilla, $topModule, $topModule2, $layer1Module, $layer2Module, $layer2Module2, $module1, $module2];
        $this->assertSame($expected, $this->uut->invoke($this->methodHandler));
    }

    /**
     * @group uut
     */
    public function testGetMigrationOrderWhenAllDependenciesHaveBeenSpecified () : void
    {

    }
}
