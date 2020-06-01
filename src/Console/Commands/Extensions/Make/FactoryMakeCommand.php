<?php

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

        if ($module === null) {
            return parent::getPath($name);
        }

        $name = str_replace(
            ['\\', '/'], '', $this->argument('name')
        );

        return $this->moduleManager->getModuleDirectory($module)."/database/factories/{$name}.php";
    }
}
