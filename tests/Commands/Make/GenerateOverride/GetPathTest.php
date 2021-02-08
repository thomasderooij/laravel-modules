<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Make\GenerateOverride;

use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ChannelMakeCommand;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class GetPathTest extends Test
{
    private $method = "getPath";

    /**
     * @group uut
     */
    public function testGetPathWithModules () : void
    {
        $moduleManager = \Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $moduleManager);

        $moduleManager->shouldReceive("isInitialised")->andReturn(true);
        $moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        // We mock all the functions that are not our unit under test, and we exclude the constructor and option function from these mocks
        $mockableFunctions = $this->getMockableClassMethods(ChannelMakeCommand::class, $this->method, [
            // We don't want to mock these methods
            "__construct", "__call", "__callStatic",
            // We also don't mock these, since these are taken from the command class itself
            "setName", "setDescription", "setHelp", "isHidden", "setHidden", "addArgument", "addOption"
        ]);
        $functionString = implode(",", $mockableFunctions);
        $command = \Mockery::mock(ChannelMakeCommand::class."[$functionString]", [
            $this->app->make('files'),
            $moduleManager
        ]);
        $command->shouldAllowMockingProtectedMethods();

        // The module should not be the vanilla module
        $command->shouldReceive("option")->withArgs(["module"])->andReturn($module = "Module");
        $command->shouldReceive("isVanilla")->withArgs([$module])->andReturn(false);
        // The module manager should know if its initialised

        // When I call the getPath method from out class
        $uut = $this->getMethodFromClass($this->method, ChannelMakeCommand::class);
        $result = $uut->invoke($command, "Modules\TestModule\Broadcasting\NewChannel");
        $expected = base_path("Modules/TestModule/Broadcasting/NewChannel.php");

        $this->assertSame($expected, $result);
    }
}
