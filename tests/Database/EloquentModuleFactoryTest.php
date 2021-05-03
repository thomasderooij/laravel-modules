<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Database;

use Illuminate\Support\Facades\Config;
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

    /**
     * @group uut
     */
    public function testResolveFactoryName () : void
    {
        $modelsDirectory = "Aggregates";
        // If I have a model in the app/models directory
        $appModel = "App\\$modelsDirectory\\MyModel";
        // And I ask for the factory name, I expect to receive a correct factory
        $expected = "Database\\Factories\\$modelsDirectory\\MyModelFactory";
        // After we do a quick module check
        $this->moduleManager->shouldReceive("getModulesNamespace")->andReturn("Modules");

        $this->assertSame($expected, EloquentModuleFactory::resolveFactoryName($appModel));
        Config::shouldReceive("get")->withArgs(["modules.models_dir", null])->andReturn($modelsDirectory);

        // If I have a model in the modules directory
        $moduleModel = "Modules\\MyModule\\$modelsDirectory\\MyModel";
        // And I ask for the factory name, I expect to receive a correct factory
        $expected = "Modules\\MyModule\\Database\\Factories\\MyModelFactory";
        $this->assertSame($expected, EloquentModuleFactory::resolveFactoryName($moduleModel));
    }
}
