<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Routing\Console\MiddlewareMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class MiddlewareMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;
}
