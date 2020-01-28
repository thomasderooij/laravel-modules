<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as OriginalCommand;
use Illuminate\Support\Str;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class FactoryMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\database\\factories\\'.$name
        );
    }
}
