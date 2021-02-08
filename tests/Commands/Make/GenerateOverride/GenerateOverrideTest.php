<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Make\GenerateOverride;

use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ChannelMakeCommand;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class GenerateOverrideTest extends Test
{
    protected $commands = [
        ChannelMakeCommand::class
    ];
}
