<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Make\GenerateOverride;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetPathTest extends GenerateOverrideTest
{
    private $method = "getPath";

    /**
     * @group uut
     */
    public function testGetPathWithModules () : void
    {
        // We iterate over all the commands implementing this function, since they should all display the same behaviour
        foreach ($this->commands as $class) {
            // First we mock the module manager
            $moduleManager = \Mockery::mock(ModuleManager::class);
            $this->instance("module.service.manager", $moduleManager);

            // These calls are made in the command constructor, so we need to place them above the command mock
            $moduleManager->shouldReceive("isInitialised")->andReturn(true);
            $moduleManager->shouldReceive("getWorkbench")->andReturn(null);

            // We mock all the functions that are not our unit under test, and we exclude the constructor and option function from these mocks
            $mockableFunctions = $this->getMockableClassMethods($class, $this->method, [
                // We don't want to mock these methods
                "__construct", "__call", "__callStatic",
                // We also don't mock these, since these are taken from the command class itself
                "setName", "setDescription", "setHelp", "isHidden", "setHidden", "addArgument", "addOption"
            ]);
            $functionString = implode(",", $mockableFunctions);
            $command = \Mockery::mock($class."[$functionString]", [
                $this->app->make('files'),
                $moduleManager
            ]);
            $command->shouldAllowMockingProtectedMethods();

            // The module should return that its module argument is TestModule
            $command->shouldReceive("option")->withArgs(["module"])->andReturn($module = "TestModule");
            // The module should not be the vanilla module
            $command->shouldReceive("isVanilla")->withArgs([$module])->andReturn(false);

            // When I call the getPath method from out class
            $uut = $this->getMethodFromClass($this->method, $class);
            $result = $uut->invoke($command, "Modules\TestModule\Broadcasting\NewChannel");
            $expected = base_path("Modules/TestModule/Broadcasting/NewChannel.php");

            $this->assertSame($expected, $result);
        }
    }

    public function testGetPathWithoutModules () : void
    {
        $class = $this->commands[0];
    }

    public function testGetPathWithWorkbench () : void
    {

    }

    public function testGetPathWithVanillaModule () : void
    {

    }
}
