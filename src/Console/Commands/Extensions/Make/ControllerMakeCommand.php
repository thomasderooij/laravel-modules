<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Routing\Console\ControllerMakeCommand as OriginalCommand;
use Illuminate\Support\Str;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;

class ControllerMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     * @return string
     *
     * @throws \InvalidArgumentException
     * @throws ConfigFileNotFoundException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new \InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->rootNamespace())) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }
}
