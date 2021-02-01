<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Mockery\MockInterface;
use Thomasderooij\LaravelModules\Factories\RouteServiceProviderFactory;

class RouteServiceProviderFactoryTest extends ServiceProviderFactoryTest
{
    public function testCreate () : void
    {
        // If I have a route service provider
        /** @var MockInterface|RouteServiceProviderFactory $uut */
        $uut = $this->getMockServiceProviderFactory("create", RouteServiceProviderFactory::class);
        $module = "MyModule";

        // I have to get the module routes root
        $root = "$module/MyModule/routes_dir";
        $uut->shouldReceive("getModuleRoutesRoot")->withArgs([$module])->andReturn($root);
        // And the service providers directory
        $serviceProvidersDir = "/service/providers/dir";
        $uut->shouldReceive("getServiceProviderDir")->withArgs([$module])->andReturn($serviceProvidersDir);
        // And the file name of our file to be
        $fileName = "new_file.kt";
        $uut->shouldReceive("getFileName")->andReturn($fileName);
        // And the stub location to base our new file on
        $stub = "/stub/location/blueprint.stub";
        $uut->shouldReceive("getStub")->andReturn($stub);
        // We next get the placeholders and their values
        $namespacePlaceholder = "ns_placeholder";
        $uut->shouldReceive("getNamespacePlaceholder")->andReturn($namespacePlaceholder);
        $moduleNamespace = "module_namespace";
        $this->moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->once()->andReturn($moduleNamespace."\\");
        $this->moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module, false])->once()->andReturn($moduleNamespace);
        $providersRoot = "providers_root";
        $uut->shouldReceive("getProvidersRoot")->andReturn($providersRoot);
        $controllerNamespaceHolder = "controller_ns_placeholder";
        $uut->shouldReceive("getControllerNamespacePlaceholder")->andReturn($controllerNamespaceHolder);
        $classNamePlaceholder = "class_name_placeholder";
        $uut->shouldReceive("getClassNamePlaceholder")->andReturn($classNamePlaceholder);
        $className = "class_name";
        $uut->shouldReceive("getClassName")->andReturn($className);
        $webFilePlaceholder = "web_file_placeholder";
        $uut->shouldReceive("getWebRouteFilePlaceholder")->andReturn($webFilePlaceholder);
        $webFile = "web_file";
        $uut->shouldReceive("getWebFile")->andReturn($webFile);
        $apiFilePlaceholder = "api_file_placeholder";
        $uut->shouldReceive("getApiRouteFilePlaceholder")->andReturn($apiFilePlaceholder);
        $apiFile = "api_file";
        $uut->shouldReceive("getApiFile")->andReturn($apiFile);

        // Then the populate file function should be called using these return values
        $uut->shouldReceive("populateFile")->withArgs([$serviceProvidersDir, $fileName, $stub, [
            $namespacePlaceholder => $moduleNamespace . "\\" . $providersRoot,
            $controllerNamespaceHolder => $moduleNamespace,
            $classNamePlaceholder => $className,
            $webFilePlaceholder => $webFile,
            $apiFilePlaceholder => $apiFile
        ]])->once();

        // When I call the function
        $uut->create($module);
    }

    public function testGetStub () : void
    {
        $uut = $this->getMethodFromClass("getStub", RouteServiceProviderFactory::class);
        $factory = Mockery::mock(RouteServiceProviderFactory::class);

        // If I ask for the stub from this package
        $stub = realpath(__DIR__ . "/../../src/Factories/stubs/routeServiceProvider.stub");

        // If should be a real file
        /** @var Filesystem $filesystem */
        $filesystem = $this->app->make('files');
        $this->assertTrue($filesystem->isFile($stub));

        // And the stub should be returned by the function
        $this->assertSame($stub, $uut->invoke($factory));
    }

    public function testGetWebFile () : void
    {
        $uut = $this->getMethodFromClass("getWebFile", RouteServiceProviderFactory::class);
        /** @var MockInterface|RouteServiceProviderFactory $uut */
        $factory = $this->getMockServiceProviderFactory("create", RouteServiceProviderFactory::class);

        $path = "modules_dir/module/routes_dir";
        $webFile = "web_file";
        $this->routeSource->shouldReceive("getWebRoute")->andReturn($webFile);
        $extension = ".kt";
        $this->routeSource->shouldReceive("getRouteFileExtension")->andReturn($extension);

        $expected = "$path/$webFile$extension";
        $this->assertSame($expected, $uut->invoke($factory, $path));
    }

    public function testGetApiFile () : void
    {
        $uut = $this->getMethodFromClass("getApiFile", RouteServiceProviderFactory::class);
        /** @var MockInterface|RouteServiceProviderFactory $uut */
        $factory = $this->getMockServiceProviderFactory("create", RouteServiceProviderFactory::class);

        $path = "modules_dir/module/routes_dir";
        $apiFile = "api_file";
        $this->routeSource->shouldReceive("getApiRoute")->andReturn($apiFile);
        $extension = ".kt";
        $this->routeSource->shouldReceive("getRouteFileExtension")->andReturn($extension);

        $expected = "$path/$apiFile$extension";
        $this->assertSame($expected, $uut->invoke($factory, $path));
    }

    public function testGetClassName () : void
    {
        $uut = $this->getMethodFromClass("getClassName", RouteServiceProviderFactory::class);
        $provider = Mockery::mock(RouteServiceProviderFactory::class);

        // I expect to get RouteServiceProvider as a classname
        $expected = "RouteServiceProvider";

        // When I ask for the classname
        $this->assertSame($expected, $uut->invoke($provider));
    }

    public function testGetControllerNamespacePlaceholder () : void
    {
        $uut = $this->getMethodFromClass("getControllerNamespacePlaceholder", RouteServiceProviderFactory::class);
        $provider = Mockery::mock(RouteServiceProviderFactory::class);

        // I expect to get {controllerNamespace} as a placeholder
        $expected = "{controllerNamespace}";

        // When I ask for the classname
        $this->assertSame($expected, $uut->invoke($provider));
    }

    public function testGetWebRouteFilePlaceholder () : void
    {
        $uut = $this->getMethodFromClass("getWebRouteFilePlaceholder", RouteServiceProviderFactory::class);
        $provider = Mockery::mock(RouteServiceProviderFactory::class);

        // I expect to get {webRouteFile} as a placeholder
        $expected = "{webRouteFile}";

        // When I ask for the classname
        $this->assertSame($expected, $uut->invoke($provider));
    }

    public function testGetApiRouteFilePlaceholder () : void
    {
        $uut = $this->getMethodFromClass("getApiRouteFilePlaceholder", RouteServiceProviderFactory::class);
        $provider = Mockery::mock(RouteServiceProviderFactory::class);

        // I expect to get {apiRouteFile} as a placeholder
        $expected = "{apiRouteFile}";

        // When I ask for the classname
        $this->assertSame($expected, $uut->invoke($provider));
    }
}
