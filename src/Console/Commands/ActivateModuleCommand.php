<?php

namespace Thomasderooij\LaravelModules\Console\Commands;

use Illuminate\Console\Command;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\ModuleAlreadyActiveException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class ActivateModuleCommand extends ModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:activate {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a module in your project';

    public function handle()
    {
        $module = $this->argument('name');

        // Check if modules are initialised and the module exists
        if (!$this->passesCheck($module)) {
            return;
        }

        // check if the module is already active
        if ($this->moduleManager->moduleIsActive($module)) {
            $this->displayModuleAlreadyActiveWarning($module);
            return;
        }

        // activate the module
        $this->moduleManager->activateModule($module);

        // if the workbench is empty, set the activated module to the workbench
        if ($this->moduleManager->getWorkBench() === null) {
            $this->moduleManager->setWorkbench($module);
        }

        $this->displayConfirmationMessage($module);
    }

    /**
     * Display a message indicated the module was already active
     *
     * @param string $module
     */
    protected function displayModuleAlreadyActiveWarning (string $module) : void
    {
        $this->warn("The module $module is already active.");
    }

    /**
     * Display a message indicated the module has been activated successfully
     *
     * @param string $module
     */
    protected function displayConfirmationMessage (string $module) : void
    {
        $this->info("The module $module has been activated.");
    }
}
