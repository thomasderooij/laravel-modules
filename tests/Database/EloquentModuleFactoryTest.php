<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Database;

use Thomasderooij\LaravelModules\Database\Factories\EloquentModuleFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class EloquentModuleFactoryTest extends Test
{
    private ModuleManager $moduleManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleManager = \Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
    }

    public function testResolveFactoryName () : void
    {
        // If I have a model in the app/models directory
        $appModel = "App\\Models\\MyModel";
        // And I ask for the factory name, I expect to receive a correct factory
        $expected = "Database\\Factories\\MyModelFactory";
        // After we do a quick module check
        $this->moduleManager->shouldReceive("getModulesDirectory")->andReturn("Modules");

        $this->assertSame($expected, EloquentModuleFactory::resolveFactoryName($appModel));

        // If I have a model in the modules directy
        $moduleModel = "Modules\\MyModule\\Models\\MyModel";
        // And I ask for the factory name, I expect to receive a correct factory
        $expected = "Modules\\MyModule\\Database\\Factories\\MyModelFactory";
        $this->assertSame($expected, EloquentModuleFactory::resolveFactoryName($moduleModel));
    }
}
