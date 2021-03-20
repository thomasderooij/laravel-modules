<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Thomasderooij\LaravelModules\Contracts\Factories\AppBootstrapFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\ConfigFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleMigrationFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\TrackerFactory;
use Thomasderooij\LaravelModules\Contracts\Services\ComposerEditor;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class InitModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize module functionality';

    /**
     * A factory to replace the application bootstrap file to use modular kernels
     */
    protected AppBootstrapFactory $bootstrapFactory;

    /**
     * The composer class
     */
    protected Composer $composer;

    /**
     * A composer editor to update the composer.json file
     */
    protected ComposerEditor $composerEditor;

    /**
     * A factory to implement the config file, update composer.json and create a module tracking file
     */
    protected ConfigFactory $configFactory;

    /**
     * The Laravel filesystem
     */
    protected Filesystem $fileSystem;

    /**
     * A module managing service to query and register module changes
     */
    protected ModuleManager $moduleManager;

    /**
     * A factory to create the initial migration to for updating the migrations table
     */
    protected ModuleMigrationFactory $moduleMigrationFactory;

    /**
     * A factory to create a tracker file
     */
    protected TrackerFactory $trackerFactory;

    public function __construct(
        AppBootstrapFactory $bootstrapFactory,
        Composer $composer,
        ComposerEditor $composerEditor,
        ConfigFactory $configFactory,
        Filesystem $filesystem,
        ModuleManager $moduleManager,
        ModuleMigrationFactory $moduleMigrationFactory,
        TrackerFactory  $trackerFactory
    )
    {
        $this->bootstrapFactory = $bootstrapFactory;
        $this->composer = $composer;
        $this->composerEditor = $composerEditor;
        $this->configFactory = $configFactory;
        $this->fileSystem = $filesystem;
        $this->moduleManager = $moduleManager;
        $this->moduleMigrationFactory = $moduleMigrationFactory;
        $this->trackerFactory = $trackerFactory;

        parent::__construct();
    }

    /**
     * Handle the module initialisation
     */
    public function handle() : void
    {
        // Check if the module is already initialised
        if ($this->moduleManager->isInitialised() === true) {
            $this->displayInitiatedErrorMessage();
            return;
        }

        // Ask for the name of the root directory
        $rootDir = $this->askForRootDir();

        // Replace the bootstrap/app.php with a new version using the composite kernel
        $this->bootstrapFactory->create();

        // Create a config and tracker file named modules.php and $rootdir and add your module root to the psr4 namespace
        //  in your composer.json file.
        try {
            $this->configFactory->create($rootDir);
            $this->trackerFactory->create($rootDir);
            $this->composerEditor->addNamespaceToAutoload($rootDir);
            $this->moduleMigrationFactory->create();
        } catch (FileNotFoundException $e) {
            $this->bootstrapFactory->undo();
            $this->configFactory->undo();
            if ($this->composerEditor->hasNamespaceInAutoload($rootDir)) {
                $this->composerEditor->removeNamespaceFromAutoload($rootDir);
            }
            $this->fileSystem->delete($this->moduleManager->getModulesDirectory());
            $this->moduleMigrationFactory->undo();
            $this->displayConfigErrorMessage($e);
            return;
        }

        // Give feedback
        $this->displayInitialisedInfoMessage();

        // Dump autoload to get the new psr-4 going
        $this->displayAutoloadInfoMessage();
        $this->composer->dumpAutoloads();
        $this->displayCompleteInfoMessage();
        $this->displayInstructionsInfoMessage();
    }

    /**
     * Ask for the module root, defaulting to "modules"
     *
     * @return string
     */
    protected function askForRootDir () : string
    {
        return $this->ask("What will be the root directory of your modules?", "modules");
    }

    /**
     * Display the error message when config file creation fails
     *
     * @param Exception $e
     */
    protected function displayConfigErrorMessage (Exception $e) : void
    {
        $this->displayErrorMessage($e);
    }

    /**
     * Display the error message when migration creation fails
     *
     * @param Exception $e
     */
    protected function displayMigrationErrorMessage (Exception $e) : void
    {
        $this->displayErrorMessage($e);
    }

    /**
     * Display a module exception as an error message in the console
     *
     * @param Exception $e
     */
    protected function displayErrorMessage (Exception $e) : void
    {
        $this->error($e->getMessage());
    }

    /**
     * Display a message indicating the module initialisation is successful
     */
    protected function displayInitialisedInfoMessage () : void
    {
        $this->info("Modules initialised.");
    }

    /**
     * Display a message indicating the module initialisation has already happened
     */
    protected function displayInitiatedErrorMessage () : void
    {
        $this->error("Modules are already initiated.");
    }

    /**
     * Display a message indicating a dump-autoload is in progress
     */
    protected function displayAutoloadInfoMessage () : void
    {
        $this->info("Dumping autoloads. This might take a minute.");
    }

    /**
     * Display initialisation complete message
     */
    protected function displayCompleteInfoMessage () : void
    {
        $this->info("You are set to go. Make sure to run migration command to get your module migrations working.");
    }

    /**
     * Give base instructions on how to make a new module
     */
    protected function displayInstructionsInfoMessage () : void
    {
        $this->info("Call for module:new your-module-name-here to create a module. For any other info, check out the readme.md file.");
    }
}
