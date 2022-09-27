<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Routing\Console\ControllerMakeCommand as OriginalCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class ControllerMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function parseModel($model): string
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->rootNamespace())) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }
}
