<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Foundation\Console\TestMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class TestMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;
}
