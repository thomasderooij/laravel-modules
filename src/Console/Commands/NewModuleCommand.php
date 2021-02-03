<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleFactory;

class NewModuleCommand extends ModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "module:new {name}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create a new module for your project";

    /**
     * @var ModuleFactory $factory
     */
    protected $factory;

    public function __construct(ModuleFactory $moduleFactory, ModuleManager $manager)
    {
        parent::__construct($manager);

        $this->factory = $moduleFactory;
    }

    public function handle() : void
    {
        // standardise module name
        $name = $this->getNameArgument();

        // Check initialisation of the modules first
        if (!$this->moduleManager->isInitialised()) {
            $this->displayInitialisationError();
            return;
        }

        // Then check if there already is a module with the same name
        if ($this->moduleManager->hasModule($name)) {
            $this->displayModuleAlreadyExistsWarning($name);
            return;
        }

        // Create a new module
        $this->factory->create($name);

        // Add the module to the tracker
        $this->moduleManager->addModule($name);

        // Set the module to the workbench
        $this->moduleManager->setWorkbench($name);

        // Give feedback
        $this->displayModuleCreatedMessage($name);
    }

    /**
     * Display a warning message indicating the module already exists
     *
     * @param string $module
     */
    public function displayModuleAlreadyExistsWarning (string $module) : void
    {
        $this->warn("Module $module already exists.");
    }

    /**
     * Display feedback for module creation
     *
     * @param string $name
     */
    protected function displayModuleCreatedMessage (string $name) : void
    {
        $this->info("Your module has been created in the " . config("modules.root") . "/$name directory.");
    }
}
