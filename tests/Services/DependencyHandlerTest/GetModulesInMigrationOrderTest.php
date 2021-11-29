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

    public function testGetMigrationOrderWhenSomeDependenciesHaveBeenSpecified () : void
    {
        // If I ask for the modules in migration order
        $this->methodHandler->shouldReceive("getTrackerContent")->andReturn([
            $dependenciesKey = "dependenciesKey" => $dependencies = [
                ["up" => $topModule = "topModule", "down" => $layer1Module = "layer1Module"],
                ["up" => $topModule2 = "topModule2", "down" => $layer1Module],
                ["up" => $layer1Module, "down" => $layer2Module = "layer2Module"],
                ["up" => $layer1Module, "down" => $layer2Module2 = "layer2Module2"],
            ],
        ]);
        // And there are some dependencies key specified
        $this->methodHandler->shouldReceive("getDependenciesKey")->andReturn($dependenciesKey);

        Config::shouldReceive("get")->withArgs(["modules.vanilla", null])->andReturn($vanilla = "Vanilla");

        // I should get all active modules
        $this->methodHandler->shouldReceive("getActiveModules")->andReturn([
            $topModule, $module1 = "module_1", $layer2Module, $layer2Module2,
            $module2 = "module_2", $topModule2, $layer1Module,
        ]);

        // Then it should ask which modules are safe to migrate
        $list = [$vanilla];
        $this->methodHandler->shouldReceive("getModulesMigratableAfterList")->withArgs([$list, $dependencies])->andReturn($firstIteration = [$topModule, $topModule2]);
        // Then it should ask again which modules are safe to migrate
        $list = array_merge($list, $firstIteration);
        $this->methodHandler->shouldReceive("getModulesMigratableAfterList")->withArgs([$list, $dependencies])->andReturn($secondIteration = [$layer1Module]);
        $list = array_merge($list, $secondIteration);
        $this->methodHandler->shouldReceive("getModulesMigratableAfterList")->withArgs([$list, $dependencies])->andReturn($thirdIteration = [$layer2Module, $layer2Module2]);
        $list = array_merge($list, $thirdIteration);
        $this->methodHandler->shouldReceive("getModulesMigratableAfterList")->withArgs([$list, $dependencies])->andReturn([]);

        // And return an array containing vanilla, and the active modules
        $expected = [$vanilla, $topModule, $topModule2, $layer1Module, $layer2Module, $layer2Module2, $module1, $module2];
        $this->assertSame($expected, $this->uut->invoke($this->methodHandler));
    }
}
