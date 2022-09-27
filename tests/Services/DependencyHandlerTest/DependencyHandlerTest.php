<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Mockery\MockInterface;
use ReflectionMethod;
use Thomasderooij\LaravelModules\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class DependencyHandlerTest extends Test
{
    /**
     * The method we're testing
     */
    protected string $method;

    /**
     * Our method, abstracted
     */
    protected ReflectionMethod $uut;

    protected MockInterface $moduleManager;
    protected MockInterface $methodHandler;
    protected MockInterface $mockFilesystem;
    protected Filesystem $filesystem;

    protected string $upKey = "up";
    protected string $downKey = "down";

    protected string $upstreamModule = "upstream";
    protected string $moduleInBetween = "middle_manager";
    protected string $downstreamModule = "downstream";
    protected string $blueCollarModule = "blue_collar";

    protected function setUp(): void
    {
        parent::setUp();

        // We create a partial mock based on the dependency handler
        $this->mockFilesystem = \Mockery::mock(Filesystem::class);
        $this->filesystem = $this->app->make(Filesystem::class);
        $mockableMethods = $this->getMockableClassMethods(DependencyHandler::class, $this->method);
        $string = implode(",", $mockableMethods);
        $this->methodHandler = \Mockery::mock(DependencyHandler::class . "[$string]", [
            $this->mockFilesystem
        ]);
        $this->methodHandler->shouldAllowMockingProtectedMethods();

        // And our method will be the unit under test
        $this->uut = $this->getMethodFromClass($this->method, DependencyHandler::class);
    }

    protected function alphabeticalByValues(array $array): array
    {
        return collect($array)->sort()->values()->toArray();
    }
}
