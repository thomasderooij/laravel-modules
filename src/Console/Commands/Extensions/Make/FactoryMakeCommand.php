<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class FactoryMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $module = $this->option("module");
        if ($module === null) {
            $module = $this->moduleManager->getWorkBench();
        }

        // If there is not module, or the module is vanilla, or the modules are not initialised, go for the default
        if ($module === null || $this->isVanilla($module) || !$this->moduleManager->isInitialised()) {
            return parent::getPath($name);
        }

        $name = str_replace(
            ['\\', '/'], '', $this->argument('name')
        );

        return $this->moduleManager->getModuleDirectory($module)."/Database/Factories/{$name}.php";
    }
}
