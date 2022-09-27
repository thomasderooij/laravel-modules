<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as OriginalCommand;
use Illuminate\Support\Str;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class FactoryMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name): string
    {
        $module = $this->getModule();

        // If there is not module, or the module is vanilla, or the modules are not initialised, go for the default
        if ($module === null || $this->isVanilla($module) || !$this->moduleManager->isInitialised()) {
            return parent::getPath($name);
        }

        $name = str_replace(
            ['\\', '/'],
            '',
            $this->argument('name')
        );

        return $this->moduleManager->getModuleDirectory($module) . "/Database/Factories/{$name}.php";
    }

    /**
     * @return string|null
     */
    protected function getModule(): ?string
    {
        $module = $this->option("module");
        if ($module === null) {
            $module = $this->moduleManager->getWorkBench();
        }

        if ($module !== null && strtolower($module) === strtolower(config("modules.vanilla")) ?? '') {
            return null;
        }

        return $module;
    }

    /**
     * Add to the parent buildClass function by checking if the default factory namespace needs to be changed
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name): string
    {
        $namespaceModel = $this->option('model')
            ? $this->qualifyModel($this->option('model'))
            : $this->qualifyModel($this->guessModelName($name));

        if (Str::startsWith($namespaceModel, $this->rootNamespace() . 'Models')) {
            $namespace = Str::beforeLast(
                'Database\\Factories\\' . Str::after($namespaceModel, $this->rootNamespace() . 'Models\\'),
                '\\'
            );
        } else {
            $namespace = 'Database\\Factories';
        }

        $module = $this->getModule();
        $moduleNamespace = $namespace;
        if ($module !== null) {
            $moduleNamespace = $this->moduleManager->getModuleNamespace($module) . 'Database\\Factories';
        }

        $replace = [
            $namespace => $moduleNamespace,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }
}
