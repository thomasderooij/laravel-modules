<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Foundation\Console\ChannelMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class ChannelMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;
}
