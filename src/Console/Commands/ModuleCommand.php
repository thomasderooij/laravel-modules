<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Illuminate\Console\Command;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;

abstract class ModuleCommand extends Command
{
    protected ModuleManager $moduleManager;

    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;

        parent::__construct();
    }

    /**
     * Run a basic test to see if the modules are initialised, and the requested module exists.
     *
     * @param string $module
     * @return bool
     */
    protected function passesCheck(string $module): bool
    {
        if (!$this->moduleManager->isInitialised()) {
            $this->displayInitialisationError();
            return false;
        }

        if (!$this->moduleManager->hasModule($module)) {
            $this->displayModuleNotFoundError($module);
            return false;
        }

        return true;
    }

    /**
     * Get the name argument
     *
     * @return string
     */
    protected function getNameArgument(): string
    {
        return $this->argument("name");
    }

    /**
     * Display a message indicating the requested module does not exist
     *
     * @param string $module
     */
    protected function displayModuleNotFoundError(string $module): void
    {
        $this->error("There is no module named \"$module\".");
    }

    /**
     * Display a message indicating the modules have not been initialised yet.
     */
    protected function displayInitialisationError(): void
    {
        $this->error("The modules need to be initialised first. You can do this by running the module:init command.");
    }
}
