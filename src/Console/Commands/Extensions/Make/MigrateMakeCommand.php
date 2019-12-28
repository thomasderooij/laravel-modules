<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand as OriginalCommand;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Composer;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class MigrateMakeCommand extends OriginalCommand
{
    protected $moduleManager;

    public function __construct (MigrationCreator $creator, Composer $composer, ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;

        if (($module = $moduleManager->getWorkBench()) !== null) {
            $this->description = $this->description . " for " . ucfirst($module);
        }

        // Add a module option to the option list
        $this->signature.= "\n{--module= : The module to which to apply this.}";
        parent::__construct($creator, $composer);
    }

    /**
     * @return bool|string|string[]|null
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                ? $this->laravel->basePath().'/'.$targetPath
                : $targetPath;
        }

        // If there is a module in the options or workbench, apply it to the module path
        $module = $this->option("module");
        if ($module === null) {
            $module = $this->moduleManager->getWorkBench();
        }

        if ($module !== null) {
            return $this->getModuleMigrationPath($module);
        }

        return parent::getMigrationPath();
    }

    /**
     * Get the module migration path
     *
     * @param string $module
     * @return string
     */
    protected function getModuleMigrationPath (string $module)
    {
        // Get the base directory where migrations should go
        $dir = $this->laravel->basePath().'/'.config("modules.root")."/$module/database/migrations";

        // If the directory does not exist, create it with rwrwr access
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }
}
