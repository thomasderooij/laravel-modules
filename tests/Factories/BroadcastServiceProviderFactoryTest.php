<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Mockery;
use Thomasderooij\LaravelModules\Factories\BroadcastServiceProviderFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class BroadcastServiceProviderFactoryTest extends ServiceProviderFactoryTest
{
    public function testCreate () : void
    {
        $moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $moduleManager);

        // When I call create a module
        $uut = $this->getMethodFromClass("create", BroadcastServiceProviderFactory::class);
        $factory = Mockery::mock(BroadcastServiceProviderFactory::class, [$this->app->make('files'), $moduleManager, $this->app->make('module.service.route_source')]);
        $factory->shouldAllowMockingProtectedMethods();
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
        $moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->andReturn("$moduleNamespace\\");
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
}
