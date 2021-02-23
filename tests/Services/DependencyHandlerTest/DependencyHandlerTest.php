<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use ReflectionMethod;
use Thomasderooij\LaravelModules\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class DependencyHandlerTest extends Test
{
    /**
     * The method we're testing
     *
     * @var string
     */
    protected $method;

    /**
     * Our method, abstracted
     *
     * @var ReflectionMethod
     */
    protected $uut;

    protected $moduleManager;
    protected $methodHandler;
    protected $mockFilesystem;
    protected $filesystem;

    protected $upKey = "up";
    protected $downKey = "down";

    protected $upstreamModule = "upstream";
    protected $moduleInBetween = "middle_manager";
    protected $downstreamModule = "downstream";
    protected $blueCollarModule = "blue_collar";

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
}
