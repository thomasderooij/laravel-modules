<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class FactoryMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;
}
