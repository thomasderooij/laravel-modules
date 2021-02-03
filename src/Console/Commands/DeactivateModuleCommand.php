<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Illuminate\Console\Command;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotActiveException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class DeactivateModuleCommand extends ModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:deactivate {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate a module in your project';

    public function handle()
    {
        $module = $this->getNameArgument();

        if (!$this->passesCheck($module)) {
            return;
        }

        if (!$this->moduleManager->moduleIsActive($module)) {
            $this->displayModuleAlreadyDeactivatedWarning($module);
            return;
        }

        $this->moduleManager->deactivateModule($module);
        $this->displayConfirmationMessage($module);
    }

    /**
     * Display a message indicating the module was already deactivated
     *
     * @param string $module
     */
    protected function displayModuleAlreadyDeactivatedWarning (string $module) : void
    {
        $this->warn("The module \"$module\" is already deactivated.");
    }

    /**
     * Display a message indicating a successful deactivation
     *
     * @param string $module
     */
    protected function displayConfirmationMessage(string $module) : void
    {
        $this->info("The module \"$module\" has been deactivated.");
    }
}
