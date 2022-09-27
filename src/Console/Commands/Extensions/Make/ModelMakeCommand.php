<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
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
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceUsedClasses($stub)
            ->replaceClass($stub, $name)
        ;
    }

    protected function replaceUsedClasses (string &$stub) : self
    {
        $stub = str_replace("{hasFactory}", config("modules.has_factory_trait"), $stub);
        $stub = str_replace("{model}", config("modules.base_model"), $stub);

        return $this;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\'.config("modules.models_dir");
    }

    protected function getStub(): string
    {
        return $this->option('pivot')
            ? $this->resolveStubPath('/stubs/model.pivot.stub')
            : $this->resolveModuleStubPath('/stubs/model.stub');
    }

    protected function resolveModuleStubPath ($stub) : string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : realpath(__DIR__."/../../../../Factories".$stub);
    }
}
