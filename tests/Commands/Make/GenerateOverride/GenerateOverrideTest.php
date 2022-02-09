<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Make\GenerateOverride;

use Mockery\MockInterface;
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
use Thomasderooij\LaravelModules\Services\ModuleManager;
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

    protected $moduleManager;
    protected $method;

    protected function setUp(): void
    {
        parent::setUp();

        // First we mock the module manager
        $this->moduleManager = \Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);

        // These calls are made in the command constructor, so we need to place them above the command mock
        $this->moduleManager->shouldReceive("isInitialised")->andReturn(true);
    }

    /**
     * @param string $class
     * @return MockInterface
     * @throws \Exception
     */
    protected function getCommand (string $class) : MockInterface
    {
        // We mock all the functions that are not our unit under test, and we exclude the constructor and option function from these mocks
        $mockableFunctions = $this->getMockableClassMethods($class, $this->method, [
            // We don't want to mock these methods
            "__construct", "__call", "__callStatic", "getDefaultName", "getDefaultDescription", "getDefinition", "getNativeDefinition",
            // We also don't mock these, since these are taken from the command class itself
            "setName", "setDescription", "setHelp", "isHidden", "setHidden", "addArgument", "addOption"
        ]);
        $functionString = implode(",", $mockableFunctions);
        $command = \Mockery::mock($class."[$functionString]", [
            $this->app->make('files'),
            $this->moduleManager
        ]);
        $command->shouldAllowMockingProtectedMethods();

        return $command;
    }
}
