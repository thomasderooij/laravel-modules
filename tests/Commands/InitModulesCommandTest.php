<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Mockery;
use Thomasderooij\LaravelModules\Factories\AppBootstrapFactory;
use Thomasderooij\LaravelModules\Factories\ConfigFactory;
use Thomasderooij\LaravelModules\Factories\ModuleMigrationFactory;
use Thomasderooij\LaravelModules\Factories\TrackerFactory;
use Thomasderooij\LaravelModules\Services\ComposerEditor;

class InitModulesCommandTest extends CommandTest
{
    private string $root = 'test_root';

    private $bootstrapFactory;
    private $composer;
    private $composerEditor;
    private $configFactory;
    private $filesystem;
    private $migrationFactory;
    private $trackerFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapFactory = Mockery::mock(AppBootstrapFactory::class);
        $this->instance('module.factory.bootstrap', $this->bootstrapFactory);
        $this->composer = Mockery::mock(Composer::class);
        $this->instance("composer", $this->composer);
        $this->composerEditor = Mockery::mock(ComposerEditor::class);
        $this->instance("module.service.composer_editor", $this->composerEditor);
        $this->configFactory = Mockery::mock(ConfigFactory::class);
        $this->instance('module.factory.config', $this->configFactory);
        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance("files", $this->filesystem);
        $this->migrationFactory = Mockery::mock(ModuleMigrationFactory::class);
        $this->instance('module.factory.migration', $this->migrationFactory);
        $this->trackerFactory = Mockery::mock(TrackerFactory::class);
        $this->instance("module.factory.tracker", $this->trackerFactory);

        // The artisan service provider is going to ask for a workbench, so that should return null
        $this->moduleManager->shouldReceive('getWorkBench')->andReturn(null);
    }

    /**
     * Here we test initialising modules when all goes well
     */
    public function testInitModules () : void
    {
        // When I run the init command
        $response = $this->artisan("module:init");
        // I expect to be asked the app directory namespace
        $response->expectsQuestion("What is the namespace of your app directory?", $namespace = "MyNamespace");
        // I expect to be asked which directory will be my modules directory
        $response->expectsQuestion("What will be the root directory of your modules?", $this->root);
        // And I expect to receive instructions after a successful initialisation
        $response->expectsOutput("You are set to go. Make sure to run migration command to get your module migrations working.");
        $response->expectsOutput("Call for module:new your-module-name-here to create a module. For any other info, check out the readme.md file.");

        // In this process, the bootstrap factory create method should be called
        $this->bootstrapFactory->shouldReceive('create')->once();

        // And the config factory create method should be called
        $this->configFactory->shouldReceive('create')->withArgs([$namespace, $this->root])->once();

        // And the migration factory create method should be called
        $this->migrationFactory->shouldReceive('create')->once();

        // And the module manager should be asked if its initialised
        $this->moduleManager->shouldReceive('isInitialised')->andReturn(false);
        $this->moduleManager->shouldReceive('isInitialised')->andReturnUsing(function () {
            static $i = 0;
            $i++;

            switch ($i) {
                case 1: return "woep";
                default: return "wop";
            }
        });

        // And the module editor should add the namespace to the autoload section in the composer file
        $this->composerEditor->shouldReceive('addNamespaceToAutoload')->once();

        // And the tracker factory should create a tracker file
        $this->trackerFactory->shouldReceive("create")->withArgs([$this->root])->once();

        // And composer should be trigger
        $this->composer->shouldReceive('dumpAutoloads')->once();

        $response->run();
    }

    /**
     * Here we test the response if we try to initialise modules more than once
     */
    public function testInitWhenModulesAreAlreadyInitialised () : void
    {
        // When I run the init command
        $response = $this->artisan("module:init");

        // If the modules are already initialised
        $this->moduleManager->shouldReceive('isInitialised')->andReturn(true);
        // I expect to be told the modules are already initialised
        $response->expectsOutput("Modules are already initiated.");

        $response->execute();
    }

    public function testConfigFactoryThrowsAFileNotFoundException () : void
    {
        // When I run the init command
        $response = $this->artisan("module:init");
        // I expect to be asked the app directory namespace
        $response->expectsQuestion("What is the namespace of your app directory?", $namespace = "MyNamespace");
        // I expect to be asked which directory will be my modules directory
        $response->expectsQuestion("What will be the root directory of your modules?", $this->root);

        // And the module manager should be asked if its initialised
        $this->moduleManager->shouldReceive('isInitialised')->andReturn(false);
        // I expect the bootstrap factory to call the create function
        $this->bootstrapFactory->shouldReceive("create");

        // And if the config factory throws an exception
        $errorMessage = "Error. Things went terribly wrong!";
        $this->configFactory->shouldReceive("create")->withArgs([$namespace, $this->root])->andThrow(FileNotFoundException::class, $errorMessage);

        // I expect the bootstrap factory to undo its create
        $this->bootstrapFactory->shouldReceive("undo");
        // And I expect the config factory to undo its create
        $this->configFactory->shouldReceive("undo");
        // The composer editor should check if it has added the module namespace to the autoload
        $this->composerEditor->shouldReceive("hasNamespaceInAutoload")->andReturn(false);

        // The module manager should fetch the modules directory
        $directory = "ModulesDirectory";
        $this->moduleManager->shouldReceive("getModulesDirectory")->andReturn($directory);
        // The filesystem should delete it
        $this->filesystem->shouldReceive("delete")->withArgs([$directory]);
        // And the migration factory should call its undo function
        $this->migrationFactory->shouldReceive("undo");

        // I expect to be be relayed the error message
        $response->expectsOutput($errorMessage);

        $response->execute();
    }
}
