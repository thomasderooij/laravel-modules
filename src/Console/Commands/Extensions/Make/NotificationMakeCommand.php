<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Foundation\Console\NotificationMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;

class NotificationMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;
}
