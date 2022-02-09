<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Db\SeedCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ChannelMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ConsoleMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ControllerMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\EventMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ExceptionMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\FactoryMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\JobMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ListenerMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\MailMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\MiddlewareMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\MigrateMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ModelMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\NotificationMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ObserverMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\PolicyMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ProviderMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\RequestMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\ResourceMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\RuleMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\SeederMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Make\TestMakeCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate\FreshCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate\MigrateCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate\RollbackCommand;

class ArtisanServiceProvider extends ServiceProvider implements DeferrableProvider
{
    protected array $makeCommands = [
        "Channel"       => \Illuminate\Foundation\Console\ChannelMakeCommand::class,
        "Controller"    => \Illuminate\Routing\Console\ControllerMakeCommand::class,
        "Console"       => \Illuminate\Foundation\Console\ConsoleMakeCommand::class,
        "Event"         => \Illuminate\Foundation\Console\EventMakeCommand::class,
        "Exception"     => \Illuminate\Foundation\Console\ExceptionMakeCommand::class,
        "Factory"       => \Illuminate\Database\Console\Factories\FactoryMakeCommand::class,
        "Job"           => \Illuminate\Foundation\Console\JobMakeCommand::class,
        "Listener"      => \Illuminate\Foundation\Console\ListenerMakeCommand::class,
        "Mail"          => \Illuminate\Foundation\Console\MailMakeCommand::class,
        "Middleware"    => \Illuminate\Routing\Console\MiddlewareMakeCommand::class,
        "Migrate"       => \Illuminate\Database\Console\Migrations\MigrateMakeCommand::class,
        "Model"         => \Illuminate\Foundation\Console\ModelMakeCommand::class,
        "Notification"  => \Illuminate\Foundation\Console\NotificationMakeCommand::class,
        "Observer"      => \Illuminate\Foundation\Console\ObserverMakeCommand::class,
        "Policy"        => \Illuminate\Foundation\Console\PolicyMakeCommand::class,
        "Provider"      => \Illuminate\Foundation\Console\ProviderMakeCommand::class,
        "Request"       => \Illuminate\Foundation\Console\RequestMakeCommand::class,
        "Resource"      => \Illuminate\Foundation\Console\ResourceMakeCommand::class,
        "Rule"          => \Illuminate\Foundation\Console\RuleMakeCommand::class,
        "Seeder"        => \Illuminate\Database\Console\Seeds\SeederMakeCommand::class,
        "Test"          => \Illuminate\Foundation\Console\TestMakeCommand::class,
    ];

    protected array $migrateCommands = [
        "Migrate" => \Illuminate\Database\Console\Migrations\MigrateCommand::class,
        "Fresh" => \Illuminate\Database\Console\Migrations\FreshCommand::class,
//        'MigrateRefresh' => 'command.migrate.refresh',
        'Rollback' => \Illuminate\Database\Console\Migrations\RollbackCommand::class,
    ];

    protected array $databaseCommands = [
        "Seed" => "command.seed"
    ];

    public function register() : void
    {
        $this->registerDatabaseCommands();
        $this->registerMakeCommands();
        $this->registerMigrateCommands();
    }

    protected function registerDatabaseCommands () : void
    {
        $this->registerSeedCommand();
    }

    protected function registerMakeCommands () : void
    {
        foreach (array_keys($this->makeCommands) as $command) {
            call_user_func_array([$this, "register{$command}MakeCommand"], []);
        }
    }

    protected function registerMigrateCommands () : void
    {
        foreach (array_keys($this->migrateCommands) as $command) {
            call_user_func_array([$this, "register{$command}MigrateCommand"], []);
        }
    }

    /*************************************************************************
     * Database commands
     *************************************************************************/
    protected function registerSeedCommand () : void
    {
        $this->app->singleton($this->databaseCommands["Seed"], function ($app) {
            return new SeedCommand(
                $app['db'],
                $app['module.service.manager'],
            );
        });
    }

    /*************************************************************************
     * Make commands
     *************************************************************************/
    protected function registerChannelMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Channel"], function ($app) {
            return new ChannelMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerControllerMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Controller"], function ($app) {
            return new ControllerMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerConsoleMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Console"], function ($app) {
            return new ConsoleMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerEventMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Event"], function ($app) {
            return new EventMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerExceptionMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Exception"], function ($app) {
            return new ExceptionMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerFactoryMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Factory"], function ($app) {
            return new FactoryMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerJobMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Job"], function ($app) {
            return new JobMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerListenerMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Listener"], function ($app) {
            return new ListenerMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerMailMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Mail"], function ($app) {
            return new MailMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerMiddlewareMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Middleware"], function ($app) {
            return new MiddlewareMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerMigrateMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Migrate"], function ($app) {
            return new MigrateMakeCommand(
                $app["files"],
                $app["migration.creator"],
                $app["composer"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerModelMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Model"], function ($app) {
            return new ModelMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerNotificationMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Notification"], function ($app) {
            return new NotificationMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerObserverMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Observer"], function ($app) {
            return new ObserverMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerPolicyMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Policy"], function ($app) {
            return new PolicyMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerProviderMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Provider"], function ($app) {
            return new ProviderMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerRequestMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Request"], function ($app) {
            return new RequestMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerResourceMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Resource"], function ($app) {
            return new ResourceMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerRuleMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Rule"], function ($app) {
            return new RuleMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerSeederMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Seeder"], function ($app) {
            return new SeederMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    protected function registerTestMakeCommand () : void
    {
        $this->app->singleton($this->makeCommands["Test"], function ($app) {
            return new TestMakeCommand(
                $app["files"],
                $app["module.service.manager"]
            );
        });
    }

    /*************************************************************************
     * Migrate commands
     *************************************************************************/
    protected function registerMigrateMigrateCommand () : void
    {
        $this->app->singleton($this->migrateCommands["Migrate"], function ($app) {
            return new MigrateCommand(
                $app["migrator"],
                $app["events"],
                $app["module.service.manager"],
                $app["module.service.dependency_handler"]
            );
        });
    }

    protected function registerFreshMigrateCommand () : void
    {
        $this->app->singleton($this->migrateCommands["Fresh"], function ($app) {
            return new FreshCommand(
                $app["module.service.manager"],
                $app["module.service.dependency_handler"]
            );
        });
    }

    protected function registerRollbackMigrateCommand () : void
    {
        $this->app->singleton($this->migrateCommands["Rollback"], function ($app) {
            return new RollbackCommand(
                $app["migrator"],
                $app["module.service.manager"]
            );
        });
    }

    public function provides() : array
    {
        return array_merge(
            array_values($this->makeCommands),
            array_values($this->migrateCommands)
        );
    }
}
