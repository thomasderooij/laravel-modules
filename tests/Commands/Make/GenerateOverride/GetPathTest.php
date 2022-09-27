<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Make\GenerateOverride;

use Mockery\MockInterface;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetPathTest extends GenerateOverrideTest
{
    protected $moduleManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->method = "getPath";
    }

    public function testGetPathWithModules(): void
    {
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        // We iterate over all the commands implementing this function, since they should all display the same behaviour
        foreach ($this->commands as $class) {
            $command = $this->getCommand($class);

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

    public function testGetPathWithoutModules(): void
    {
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        foreach ($this->commands as $class) {
            $command = $this->getCommand($class);

            // The module should return that there is no module
            $command->shouldReceive("option")->withArgs(["module"])->andReturn(null);
            // It should then make its parent call
            $name = "App\Broadcasting\NewChannel";
            $command->shouldReceive("parentCall")->withArgs([$this->method, [$name]])->andReturn(
                $parentResult = base_path("App/Broadcasting/NewChannel.php")
            );

            // When I call the getPath method from out class, it should return its parent call results to me
            $uut = $this->getMethodFromClass($this->method, $class);
            $result = $uut->invoke($command, $name);
            $expected = $parentResult;

            $this->assertSame($expected, $result);
        }
    }

    public function testGetPathWithWorkbench(): void
    {
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn($module = "MyModule");

        foreach ($this->commands as $class) {
            $command = $this->getCommand($class);

            // The module should return that there is no module
            $command->shouldReceive("option")->withArgs(["module"])->andReturn(null);
            // The module should not be the vanilla module
            $command->shouldReceive("isVanilla")->withArgs([$module])->andReturn(true);

            // It should then make its parent call
            $name = "App\Broadcasting\NewChannel";
            $command->shouldReceive("parentCall")->withArgs([$this->method, [$name]])->andReturn(
                $parentResult = base_path("App/Broadcasting/NewChannel.php")
            );

            // When I call the getPath method from out class, it should return its parent call results to me
            $uut = $this->getMethodFromClass($this->method, $class);
            $result = $uut->invoke($command, $name);
            $expected = $parentResult;

            $this->assertSame($expected, $result);
        }
    }

    public function testGetPathWithVanillaModule(): void
    {
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        foreach ($this->commands as $class) {
            $command = $this->getCommand($class);

            // The module should return that there is no module
            $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);
            $command->shouldReceive("option")->withArgs(["module"])->andReturn(null);
            // It should then make its parent call
            $name = "App\Broadcasting\NewChannel";
            $command->shouldReceive("parentCall")->withArgs([$this->method, [$name]])->andReturn(
                $parentResult = base_path("App/Broadcasting/NewChannel.php")
            );

            // When I call the getPath method from out class, it should return its parent call results to me
            $uut = $this->getMethodFromClass($this->method, $class);
            $result = $uut->invoke($command, $name);
            $expected = $parentResult;

            $this->assertSame($expected, $result);
        }
    }
}
