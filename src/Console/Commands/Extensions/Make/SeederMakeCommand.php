<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Database\Console\Seeds\SeederMakeCommand as OriginalCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class SeederMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    public function __construct(Filesystem $files, ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;

        if ($moduleManager->isInitialised() && ($module = $moduleManager->getWorkBench()) !== null) {
            $this->description = $this->description . " for " . ucfirst($module);
        }

        parent::__construct($files);
    }

    protected function getStub(): string
    {
        if ($this->getModule() !== null) {
            return realpath(__DIR__ . '/../../../../Factories/stubs/seeder.stub');
        }
        return parent::getStub();
    }

    /**
     * This provides a namespace as an argument
     *
     * @param $name
     * @return string
     */
    protected function getPath($name): string
    {
        $name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));
        // If there is no module, return default values
        $module = $this->getModule();
        // If there is not module, or the module is vanilla, or the modules are not initialised, go for the default
        if ($module === null) {
            return $this->parentCall("getPath", [$name]);
        }

        // Parse the namespace to a directory location
        $base = $this->moduleManager->getModuleDirectory($module);

        return $base . "/Database/Seeders/$name.php";
    }

    protected function getNamespace($name): string
    {
        $module = $this->getModule();
        if ($module !== null) {
            return $this->moduleManager->getModuleNamespace($module) . "Database\Seeders";
        }

        return parent::getNamespace($name);
    }
}
