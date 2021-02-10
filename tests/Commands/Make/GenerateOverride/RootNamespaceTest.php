<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Make\GenerateOverride;

class RootNamespaceTest extends GenerateOverrideTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->method = "rootNamespace";
    }

    public function testRootNamespaceWithoutModules () : void
    {
        // If I ask for the namespace, and there is no module
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        foreach ($this->commands as $class) {
            $command = $this->getCommand($class);
            $command->shouldReceive("option")->withArgs(["module"])->andReturn(null);

            // The parent call should be made
            $command->shouldReceive("parentCall")->withArgs([$this->method])->andReturn($namespace = "Namespace");

            // And that result should be returned
            $uut = $this->getMethodFromClass($this->method, $class);
            $result = $uut->invoke($command);
            $this->assertSame($namespace, $result);
        }
    }

    public function testRootNamespaceWithVanillaModule () : void
    {
        // If I ask for the namespace, and there is no module
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        foreach ($this->commands as $class) {
            $command = $this->getCommand($class);
            $command->shouldReceive("option")->withArgs(["module"])->andReturn("Vanilla");

            $command->shouldReceive("isVanilla")->andReturn(true);
            // The parent call should be made
            $command->shouldReceive("parentCall")->withArgs([$this->method])->andReturn($namespace = "Namespace");

            // And that result should be returned
            $uut = $this->getMethodFromClass($this->method, $class);
            $result = $uut->invoke($command);
            $this->assertSame($namespace, $result);
        }
    }

    public function testRootNamespaceWithModuleOption () : void
    {
        // If I ask for the namespace, and there is no module
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn(null);

        foreach ($this->commands as $class) {
            $command = $this->getCommand($class);
            $command->shouldReceive("option")->withArgs(["module"])->andReturn($module = "myModule");

            // The module manager should return the namespace
            $command->shouldReceive("isVanilla")->andReturn(false);
            $this->moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->andReturn($namespace = "Namespace");

            // And that result should be returned
            $uut = $this->getMethodFromClass($this->method, $class);
            $result = $uut->invoke($command);
            $this->assertSame($namespace, $result);
        }
    }

    public function testRootNamespaceWithModuleInWorkbench () : void
    {
        // If I ask for the namespace, and there is no module
        $this->moduleManager->shouldReceive("getWorkbench")->andReturn($module = "MyModule");

        foreach ($this->commands as $class) {
            $command = $this->getCommand($class);
            $command->shouldReceive("option")->withArgs(["module"])->andReturn(null);

            // The module manager should return the namespace
            $command->shouldReceive("isVanilla")->andReturn(false);
            $this->moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->andReturn($namespace = "Namespace");

            // And that result should be returned
            $uut = $this->getMethodFromClass($this->method, $class);
            $result = $uut->invoke($command);
            $this->assertSame($namespace, $result);
        }
    }
}
