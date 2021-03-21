<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Foundation\Console\ModelMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class ModelMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceFactoryTrait($stub)->replaceClass($stub, $name);
    }

    protected function replaceFactoryTrait (string &$stub) : self
    {
        $stub = str_replace(
            "Illuminate\Database\Eloquent\Factories\HasFactory",
            "Thomasderooij\LaravelModules\Database\Factories\HasFactory",
            $stub
        );

        return $this;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\'.config("modules.models_dir");
    }
}
