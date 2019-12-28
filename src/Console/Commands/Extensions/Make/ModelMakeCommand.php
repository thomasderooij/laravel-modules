<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Foundation\Console\ModelMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class ModelMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;
}
