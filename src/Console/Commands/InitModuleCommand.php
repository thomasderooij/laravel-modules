<?php

namespace Thomasderooij\LaravelModules\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Composer;
use Thomasderooij\LaravelModules\Contracts\Factories\AppBootstrapFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\ConfigFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleMigrationFactory;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\ModuleException;

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
     * The composer class
     *
     * @var Composer
     */
    protected $composer;

    /**
     * A factory to replace the application bootstrap file to use modular kernels
     *
     * @var AppBootstrapFactory
     */
    protected $bootstrapFactory;

    /**
     * A factory to implement the config file, update composer.json and create a module tracking file
     *
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * A factory to create the initial migration to for updating the migrations table
     *
     * @var ModuleMigrationFactory
     */
    protected $moduleMigrationFactory;

    /**
     * A module managing service to query and register module changes
     *
     * @var ModuleManager
     */
    protected $moduleManager;

    public function __construct(
        Composer $composer,
        AppBootstrapFactory $bootstrapFactory,
        ModuleMigrationFactory $moduleMigrationFactory,
        ConfigFactory $configFactory,
        ModuleManager $moduleManager
    )
    {
        $this->composer = $composer;
        $this->bootstrapFactory = $bootstrapFactory;
        $this->moduleMigrationFactory = $moduleMigrationFactory;
        $this->configFactory = $configFactory;
        $this->moduleManager = $moduleManager;

        parent::__construct();
    }

    /**
     * Handle the module initialisation
     *
     * @throws FileNotFoundException
     */
    public function handle()
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
        } catch (ModuleException $e) {
            $this->bootstrapFactory->undo();
            $this->moduleManager->removeModuleDirectory();
            $this->displayConfigErrorMessage($e);
            return;
        }

        // Create a migration file to track module migrations
        try {
            $this->moduleMigrationFactory->create();
        } catch (ModuleException $e) {
            $this->bootstrapFactory->undo();
            $this->configFactory->undo();
            $this->moduleManager->removeModuleDirectory();
            $this->displayMigrationErrorMessage($e);
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
     * @param ModuleException $e
     */
    protected function displayConfigErrorMessage (ModuleException $e) : void
    {
        $this->displayErrorMessage($e);
    }

    /**
     * Display the error message when migration creation fails
     *
     * @param ModuleException $e
     */
    protected function displayMigrationErrorMessage (ModuleException $e) : void
    {
        $this->displayErrorMessage($e);
    }

    /**
     * Display a module exception as an error message in the console
     *
     * @param ModuleException $e
     */
    protected function displayErrorMessage (ModuleException $e) : void
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
