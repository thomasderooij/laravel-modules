<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Database\Console\Seeds\SeederMakeCommand as OriginalCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class SeederMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    public function __construct(Filesystem $files, Composer $composer, ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;

        if ($moduleManager->isInitialised() && ($module = $moduleManager->getWorkBench()) !== null) {
            $this->description = $this->description . " for " . ucfirst($module);
        }

        parent::__construct($files, $composer);
    }
}
