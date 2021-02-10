<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Make\GenerateOverride;

use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ChannelMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ConsoleMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\EventMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ExceptionMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\JobMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ListenerMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\MailMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\MiddlewareMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ModelMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\NotificationMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ObserverMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\PolicyMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ProviderMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\RequestMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ResourceMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\RuleMakeCommand;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class GenerateOverrideTest extends Test
{
    protected $commands = [
        ChannelMakeCommand::class,
        ConsoleMakeCommand::class,
        EventMakeCommand::class,
        ExceptionMakeCommand::class,
        JobMakeCommand::class,
        ListenerMakeCommand::class,
        MailMakeCommand::class,
        MiddlewareMakeCommand::class,
        ModelMakeCommand::class,
        NotificationMakeCommand::class,
        ObserverMakeCommand::class,
        PolicyMakeCommand::class,
        ProviderMakeCommand::class,
        RequestMakeCommand::class,
        ResourceMakeCommand::class,
        RuleMakeCommand::class,
    ];
}
