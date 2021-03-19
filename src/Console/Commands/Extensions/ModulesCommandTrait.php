<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler;
use Thomasderooij\LaravelModules\ParentCallTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

trait ModulesCommandTrait
{
    use ParentCallTrait;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var DependencyHandler
     */
    protected $dependencyHandler;

    /**
     * Get modules from either a command option, or return your workbench module, or return an empty array
     *
     * @return array
     */
    protected function getModules () : array
    {
        $modulesString = $this->option("modules") ?: null;
        if ($modulesString !== null) {
            return $this->parseModulesString($modulesString);
        }

        $this->dependencyHandler->getModulesInMigrationOrder();

        if (($module = $this->moduleManager->getWorkBench()) !== null) {
            return [$module];
        }

        return [config("modules.vanilla")];
    }

    /**
     * Get your module from either an option, or your workbench. If neither are present, return null;
     *
     * @return string|null
     */
    protected function getModule ()
    {
        $module = $this->option("module") ?: null;
        if ($module !== null) {
            return $module;
        }

        if ($this->moduleManager->getWorkBench() !== null) {
            return $this->moduleManager->getWorkBench();
        }

        return null;
    }

    /**
     * Parse the module string into an array of module names
     *
     * @param string $string
     * @return array
     */
    protected function parseModulesString (string $string) : array
    {
        return explode(",", $string);
    }

    /**
     * Display a module not found warning
     *
     * @param string $module
     */
    protected function displayModuleNotFoundWarning (string $module) : void
    {
        $this->warn("Module \"$module\" does not exist.");
    }

    /**
     * Display a modules no initialised error
     *
     * @param string $errorMessage
     */
    protected function displayModulesNotInitialisedError (string $errorMessage) : void
    {
        $this->error($errorMessage);
    }
}
