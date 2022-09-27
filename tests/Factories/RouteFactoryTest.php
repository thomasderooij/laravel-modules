<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Factories\RouteFactory;
use Thomasderooij\LaravelModules\Services\RouteSource;
use Thomasderooij\LaravelModules\Tests\Test;

class RouteFactoryTest extends Test
{
    public function testCreate(): void
    {
        $filesystem = Mockery::mock(Filesystem::class);
        $routeSource = Mockery::mock(RouteSource::class);

        // When I create route files for a module
        $module = "NewModule";
        $factory = Mockery::mock(
            RouteFactory::class,
            [$filesystem, $this->app->make("module.service.manager"), $routeSource]
        );
        $factory->shouldAllowMockingProtectedMethods();
        $uut = $this->getMethodFromClass("create", RouteFactory::class);

        // I should fetch the route directory
        $factory->shouldReceive("getRouteDirectory")->withArgs([$module])->andReturn(
            $routeDirectory = "route_directory"
        );

        // I should get the route directory
        $filesystem->shouldReceive("exists")->withArgs([$routeDirectory])->andReturn(false);
        // And create it if it doesn't exist
        $filesystem->shouldReceive("makeDirectory")->withArgs([$routeDirectory, 0755, true])->once();

        // I should then get my route files
        $routeFiles = ["web_file", "api_file", "console_file"];
        $routeSource->shouldReceive("getRouteFiles")->andReturn($routeFiles)->once();

        $factory->shouldReceive("createRouteFile")->withArgs([$routeFiles[0], $routeDirectory]);
        $factory->shouldReceive("createRouteFile")->withArgs([$routeFiles[1], $routeDirectory]);
        $factory->shouldReceive("createRouteFile")->withArgs([$routeFiles[2], $routeDirectory]);

        $uut->invoke($factory, $module);
    }

    public function testGetRouteDirectory(): void
    {
        $routeSource = Mockery::mock(RouteSource::class);

        // When I get the route directory for a module
        $module = "NewModule";
        $factory = Mockery::mock(
            RouteFactory::class,
            [$this->app->make("files"), $this->app->make("module.service.manager"), $routeSource]
        );
        $factory->shouldAllowMockingProtectedMethods();
        $uut = $this->getMethodFromClass("getRouteDirectory", RouteFactory::class);

        // The config should get the modules root
        $root = "ModulesRoot";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root);

        // And the route source should get the route dir
        $routeDir = "route_dir";
        $routeSource->shouldReceive("getRouteRootDir")->andReturn($routeDir);

        // And I expect to receive the following path
        $expected = base_path("$root/$module/$routeDir");
        $this->assertSame($expected, $uut->invoke($factory, $module));
    }

    public function testCreateRouteFile(): void
    {
        $routeSource = Mockery::mock(RouteSource::class);

        // When create a route file for a module
        $factory = Mockery::mock(
            RouteFactory::class,
            [$this->app->make("files"), $this->app->make("module.service.manager"), $routeSource]
        );
        $factory->shouldAllowMockingProtectedMethods();
        $uut = $this->getMethodFromClass("createRouteFile", RouteFactory::class);

        $directory = "route_directory";
        // I need to fetch a stub file first
        $fileName = "test_routes";
        $factory->shouldReceive("getStubByType")->withArgs([$fileName])->andReturn($stub = "path/to/stub");

        // And I need a file extension
        $routeSource->shouldReceive("getRouteFileExtension")->andReturn($extension = ".kt");

        // And then I need to call the populateFile function
        $factory->expects("populateFile")->withArgs([
            $directory,
            "$fileName$extension",
            $stub,
            [
                "{typeUcfirst}" => ucfirst($fileName),
                "{type}" => $fileName,
                "{middleware}" => $fileName,
            ]
        ]);

        $uut->invoke($factory, $fileName, $directory);
    }

    public function testGetStubByType(): void
    {
        $routeSource = Mockery::mock(RouteSource::class);

        // When create a route file for a module
        $factory = Mockery::mock(
            RouteFactory::class,
            [$this->app->make("files"), $this->app->make("module.service.manager"), $routeSource]
        );
        $factory->shouldAllowMockingProtectedMethods();
        $uut = $this->getMethodFromClass("getStubByType", RouteFactory::class);

        $webRoute = "web_route";
        $routeSource->shouldReceive("getWebRoute")->andReturn($webRoute);
        $apiRoute = "api_route";
        $routeSource->shouldReceive("getApiRoute")->andReturn($apiRoute);
        $consoleRoute = "console_route";
        $routeSource->shouldReceive("getConsoleRoute")->andReturn($consoleRoute);

        $commonStubLocation = realpath(__DIR__ . "/../../src/Factories/stubs/routes/common.stub");
        $consoleStubLocation = realpath(__DIR__ . "/../../src/Factories/stubs/routes/console.stub");
        $emptyStubLocation = realpath(__DIR__ . "/../../src/Factories/stubs/routes/empty.stub");

        // If I ask for the web stub, I should receive the common stub
        $this->assertSame($commonStubLocation, $uut->invoke($factory, $webRoute));
        // If I ask for the api stub, I should receive the common stub
        $this->assertSame($commonStubLocation, $uut->invoke($factory, $apiRoute));
        // If I ask for the console stub, I should receive the console stub
        $this->assertSame($consoleStubLocation, $uut->invoke($factory, $consoleRoute));
        // And other should return an empty stub
        $this->assertSame($emptyStubLocation, $uut->invoke($factory, "other"));
    }
}
