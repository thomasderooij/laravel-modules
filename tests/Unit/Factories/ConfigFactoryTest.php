<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Contracts\Factories\ConfigFactory as ConfigFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Services\ComposerEditor as ComposerEditorContract;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as ModuleManagerContract;
use Thomasderooij\LaravelModules\Factories\ConfigFactory;
use Thomasderooij\LaravelModules\Services\ComposerEditor;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class ConfigFactoryTest extends Test
{
    private $rootDir = "test_root";

    /**
     * Here we test if the create function calls the expected protected functions and dependencies
     */
    public function testCreate () : void
    {
        $mockEditor = Mockery::mock(ComposerEditor::class);

        // If I have a config factory
        /** @var Mockery\MockInterface&ConfigFactory $uut */
        $uut = Mockery::mock(ConfigFactory::class.'[createConfigFile,, replaceServiceProviders]', [
            $this->app->make(Filesystem::class),
            $this->app->make(ModuleManager::class),
            $mockEditor,
        ]);

        // The following methods should be called
        $uut->shouldAllowMockingProtectedMethods();
        $uut->shouldReceive('createConfigFile')->withArgs([$this->rootDir])->once();
        $uut->shouldReceive('replaceServiceProviders')->once();

        // When I call the create function
        $uut->create($this->rootDir);
    }

    /**
     * Here we test if the undo function call all the arguments we expect it to call
     */
    public function testUndo () : void
    {
        $mockEditor = Mockery::mock(ComposerEditor::class);

        // If I have a config factory
        /** @var Mockery\MockInterface&ConfigFactory $uut */
        $uut = Mockery::mock(ConfigFactory::class.'[removeConfigFile, revertServiceProviders]', [
            $this->app->make(Filesystem::class),
            $this->app->make(ModuleManager::class),
            $mockEditor,
        ]);

        // The following methods should be called
        $uut->shouldAllowMockingProtectedMethods();
        $uut->shouldReceive('revertServiceProviders')->once();
        $uut->shouldReceive('removeConfigFile')->once();

        // When I call the undo function
        $uut->undo();
    }

    public function testCreateConfigFile () : void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $this->instance(Filesystem::class, $mockFilesystem);
        $this->instance(ModuleManagerContract::class, $this->app->make(ModuleManager::class));
        $this->instance(ComposerEditorContract::class, $this->app->make(ComposerEditor::class));

        // If I have a config factory
        /** @var ConfigFactoryContract $uut */
        $reflection = new \ReflectionClass(ConfigFactory::class);
        $uut = $reflection->getMethod("createConfigFile"); // The unit under test is this specific method
        $uut->setAccessible(true);

        // A stub file for the config file should be fetched
        $configStub = realpath(__DIR__ . '/../../../src/Factories/stubs/config.stub');
        $mockFilesystem
            ->shouldReceive('get')
            ->withArgs([$configStub])
            ->andReturn(["Config stub content"]) // We don't care about the content. That's something the file factory manages
            ->once()
        ;

        // And a new config file should be created, based on the stub
        $configFileArgument = null; // This variable will be used to capture the argument content
        $mockFilesystem
            ->shouldReceive('put')
            ->withArgs([
                base_path('config/modules.php'),
                Mockery::capture($configFileArgument)
            ])
            ->once()
        ;

        // When I call the createConfigFile method with a rootdir as argument
        $factory = $this->app->make(ConfigFactory::class);
        $uut->invoke($factory, $this->rootDir);

        $this->assertMatchesSnapshot($configFileArgument);
    }

    public function testReplaceServiceProviders () : void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $this->instance(Filesystem::class, $mockFilesystem);
        $this->instance(ModuleManagerContract::class, $this->app->make(ModuleManager::class));
        $this->instance(ComposerEditorContract::class, $this->app->make(ComposerEditor::class));

        // If I have a config factory
        /** @var ConfigFactoryContract $uut */
        $reflection = new \ReflectionClass(ConfigFactory::class);
        $uut = $reflection->getMethod("replaceServiceProviders"); // The unit under test is this specific method
        $uut->setAccessible(true);

        $mockFilesystem
            ->shouldReceive('get')
            ->withArgs([config_path('app.php')])
            ->andReturn(['File content']) // We don't care about the content
            ->once()
        ;

        $appFileArgument = null;
        $mockFilesystem
            ->shouldReceive('put')
            ->withArgs([
                config_path('app.php'),
                Mockery::capture($appFileArgument)
            ])
            ->once()
        ;

        // When I call the replaceServiceProviders method
        $factory = $this->app->make(ConfigFactory::class);
        $uut->invoke($factory, $this->rootDir);

        $this->assertMatchesSnapshot($appFileArgument);
    }

    public function testRevertServiceProviders () : void
    {

    }

    public function testRemoveConfigFile () : void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $this->instance(Filesystem::class, $mockFilesystem);
        $this->instance(ModuleManagerContract::class, $this->app->make(ModuleManager::class));
        $this->instance(ComposerEditorContract::class, $this->app->make(ComposerEditor::class));

        // If I have a config factory
        /** @var ConfigFactoryContract $uut */
        $reflection = new \ReflectionClass(ConfigFactory::class);
        $uut = $reflection->getMethod("removeConfigFile"); // The unit under test is this specific method
        $uut->setAccessible(true);

        $mockFilesystem
            ->shouldReceive('delete')
            ->withArgs([config_path("modules.php")])
            ->once()
        ;

        // When I call the replaceServiceProviders method
        $factory = $this->app->make(ConfigFactory::class);
        $uut->invoke($factory, $this->rootDir);
    }
}
