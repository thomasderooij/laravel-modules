<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Factories\BroadcastServiceProviderFactory;

class BroadcastServiceProviderFactoryTest extends ServiceProviderFactoryTest
{
    public function testCreate () : void
    {
        // When I call create a module
        $uut = $this->getMethodFromClass("create", BroadcastServiceProviderFactory::class);
        $factory = $this->getMockServiceProviderFactory("create", BroadcastServiceProviderFactory::class);
        $module = "NewModule";

        // I should get the modules route root
        $routesRoot = "routes_root";
        $factory->shouldReceive("getModuleRoutesRoot")->andReturn($routesRoot);
        // And I should get the service provider dir
        $serviceProviderDir = "service_providers";
        $factory->shouldReceive("getServiceProviderDir")->andReturn($serviceProviderDir);
        // And I should get the file name
        $fileName = "file_name";
        $factory->shouldReceive("getFileName")->andReturn($fileName);
        // And I should get a stub
        $stub = "stub_content";
        $factory->shouldReceive("getStub")->andReturn($stub);
        // I should also get a namespace placeholder
        $namespacePlaceholder = "ns_placeholder";
        $factory->shouldReceive("getNamespacePlaceholder")->andReturn($namespacePlaceholder);
        // And the module manager should provide a namespace for the module
        $moduleNamespace = "Module\\Stuff";
        $this->moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->andReturn("$moduleNamespace\\");
        // I should also get the providers root
        $providersRoot = "providers_root";
        $factory->shouldReceive("getProvidersRoot")->andReturn($providersRoot);
        // And I should get the classname placeholder
        $classNamePlaceholder = "class_name_placeholder";
        $factory->shouldReceive("getClassNamePlaceholder")->andReturn($classNamePlaceholder);
        // And I should get the class
        $class = "classy_class";
        $factory->shouldReceive("getClassName")->andReturn($class);
        // And I should get the route file placeholder
        $routeFilePlaceholder = "route_file_placeholder";
        $factory->shouldReceive("getRouteFilePlaceholder")->andReturn($routeFilePlaceholder);
        // And I should get the relative route file
        $routeFile = "route_file.php";
        $factory->shouldReceive("getRelativeRouteFile")->withArgs([$routesRoot])->andReturn($routeFile);

        // And finally, the populate file function should be called, which is tested in the FileFactoryTest
        $factory->shouldReceive("populateFile")->withArgs([$serviceProviderDir, $fileName, $stub, [
            $namespacePlaceholder => "$moduleNamespace\\$providersRoot",
            $classNamePlaceholder => $class,
            $routeFilePlaceholder => $routeFile
        ]]);

        $uut->invoke($factory, $module);
    }

    public function testGetStub () : void
    {
        $uut = $this->getMethodFromClass("getStub", BroadcastServiceProviderFactory::class);
        $filesystem = $this->app->make(Filesystem::class);
        $factory = Mockery::mock(BroadcastServiceProviderFactory::class);

        // If I ask for the stub from this package
        $stub = realpath(__DIR__ . "/../../src/Factories/stubs/broadcastServiceProvider.stub");

        // If should be a real file
        $this->assertTrue($filesystem->isFile($stub));

        // And the stub should be returned by the function
        $this->assertSame($stub, $uut->invoke($factory));
    }

    public function testGetClassName () : void
    {
        $uut = $this->getMethodFromClass("getClassName", BroadcastServiceProviderFactory::class);
        $factory = $this->getMockServiceProviderFactory("create", BroadcastServiceProviderFactory::class);

        // I expect to receive BroadcastServiceProvider
        $expected = "BroadcastServiceProvider";

        // When I ask for the classname
        $this->assertSame($expected, $uut->invoke($factory));
    }

    public function testGetRelativeRoutesDir () : void
    {
        $uut = $this->getMethodFromClass("getRelativeRouteFile", BroadcastServiceProviderFactory::class);
        $factory = $this->getMockServiceProviderFactory("create", BroadcastServiceProviderFactory::class);

        // If I want to get a routes channels file for my service provider
        $root = "Modules/routes_path";

        // The route source service should give me the name of the channels file
        $channelsFileName = "channels_file";
        $this->routeSource->shouldReceive("getChannelsRoute")->andReturn($channelsFileName);
        // And if should give me the file extension
        $extension = ".kt";
        $this->routeSource->shouldReceive("getRouteFileExtension")->andReturn($extension);

        // And I expect the relative path to the channels file
        $expected = "$root/$channelsFileName$extension";

        $this->assertSame($expected, $uut->invoke($factory, $root));
    }

    public function testGetRouteFilePlaceholder () : void
    {
        $uut = $this->getMethodFromClass("getRouteFilePlaceholder", BroadcastServiceProviderFactory::class);
        $factory = $this->getMockServiceProviderFactory("create", BroadcastServiceProviderFactory::class);

        // I expect to receive BroadcastServiceProvider
        $expected = "{routeFile}";

        // When I ask for the classname
        $this->assertSame($expected, $uut->invoke($factory));
    }
}
