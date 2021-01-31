<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories\ServiceProviderFactory;

class CreateTest extends ServiceProviderFactoryTest
{
    private $method = "create";

    /**
     * @group uut
     */
    public function testCreate () : void
    {
        // When I call create a module
        $uut = $this->getMockServiceProviderFactory($this->method);
        $module = "NewModule";

        // I should get the service provider dir
        $serviceProviderDir = "service_providers";
        $uut->shouldReceive("getServiceProviderDir")->andReturn($serviceProviderDir);
        // And I should get the file name
        $fileName = "file_name";
        $uut->shouldReceive("getFileName")->andReturn($fileName);
        // And I should get a stub
        $stub = "stub_content";
        $uut->shouldReceive("getStub")->andReturn($stub);
        // I should also get a namespace placeholder
        $namespacePlaceholder = "ns_placeholder";
        $uut->shouldReceive("getNamespacePlaceholder")->andReturn($namespacePlaceholder);
        // And the module manager should provide a namespace for the module
        $moduleNamespace = "Module\\Stuff";
        $this->moduleManager->shouldReceive("getModuleNamespace")->withArgs([$module])->andReturn("$moduleNamespace\\");
        // I should also get the providers root
        $providersRoot = "providers_root";
        $uut->shouldReceive("getProvidersRoot")->andReturn($providersRoot);
        // And I should get the classname placeholder
        $classNamePlaceholder = "class_name_placeholder";
        $uut->shouldReceive("getClassNamePlaceholder")->andReturn($classNamePlaceholder);
        // And I should get the class
        $class = "classy_class";
        $uut->shouldReceive("getClassName")->andReturn($class);

        // And finally, the populate file function should be called, which is tested in the FileFactoryTest
        $uut->shouldReceive("populateFile")->withArgs([$serviceProviderDir, $fileName, $stub, [
            $namespacePlaceholder => "$moduleNamespace\\$providersRoot",
            $classNamePlaceholder => $class
        ]]);

        $uut->create($module);
    }
}
