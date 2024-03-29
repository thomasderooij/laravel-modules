<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Thomasderooij\LaravelModules\Console\Commands\ActivateModuleCommand;
use Thomasderooij\LaravelModules\Console\Commands\AddDependencyCommand;
use Thomasderooij\LaravelModules\Console\Commands\CheckWorkbenchCommand;
use Thomasderooij\LaravelModules\Console\Commands\DeactivateModuleCommand;
use Thomasderooij\LaravelModules\Console\Commands\DeleteDependencyCommand;
use Thomasderooij\LaravelModules\Console\Commands\DeleteModuleCommand;
use Thomasderooij\LaravelModules\Console\Commands\DependenciesCommand;
use Thomasderooij\LaravelModules\Console\Commands\InitModuleCommand;
use Thomasderooij\LaravelModules\Console\Commands\NewModuleCommand;
use Thomasderooij\LaravelModules\Console\Commands\SetWorkbenchCommand;
use Thomasderooij\LaravelModules\Console\Commands\UnsetWorkbenchCommand;

class ConsoleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    protected array $moduleCommands = [
        "Activate" => "module.command.activate",
        "AddDependency" => "module.command.add_dependency",
        "Check" => "module.command.check",
        "Deactivate" => "module.command.deactivate",
        "Delete" => "module.command.delete",
        "DeleteDependency" => "module.command.delete_dependency",
        "Dependencies" => "module.command.dependencies",
        "Init" => "module.command.init",
        "New" => "module.command.new",
        "Set" => "module.command.set",
        "Unset" => "module.command.unset",
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $commands = array_values($this->moduleCommands);
        $this->commands($commands);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerModuleCommands();
    }

    /**
     *******************************************************************************************
     * Module package command aggregator
     *******************************************************************************************
     *
     * These services should be triggered by the loop in the registerServices function
     */
    protected function registerModuleCommands(): void
    {
        foreach (array_keys($this->moduleCommands) as $key) {
            call_user_func([$this, "create" . $key . "Command"]);
        }
    }

    /**
     *******************************************************************************************
     * Module package commands
     *******************************************************************************************
     *
     * These commands should be triggered by the loop in the registerModuleCommands function
     */

    protected function createActivateCommand(): void
    {
        $this->app->singleton($this->moduleCommands["Activate"], function ($app) {
            return new ActivateModuleCommand(
                $app["module.service.manager"]
            );
        });
    }

    protected function createAddDependencyCommand(): void
    {
        $this->app->singleton($this->moduleCommands["AddDependency"], function ($app) {
            return new AddDependencyCommand(
                $app["module.service.manager"],
                $app["module.service.dependency_handler"]
            );
        });
    }

    protected function createCheckCommand(): void
    {
        $this->app->singleton($this->moduleCommands["Check"], function ($app) {
            return new CheckWorkbenchCommand(
                $app["module.service.manager"]
            );
        });
    }

    protected function createDeactivateCommand(): void
    {
        $this->app->singleton($this->moduleCommands["Deactivate"], function ($app) {
            return new DeactivateModuleCommand(
                $app["module.service.manager"]
            );
        });
    }

    protected function createDeleteCommand(): void
    {
        $this->app->singleton($this->moduleCommands["Delete"], function ($app) {
            return new DeleteModuleCommand(
                $app["module.service.manager"],
                $app["module.service.dependency_handler"]
            );
        });
    }

    protected function createDeleteDependencyCommand(): void
    {
        $this->app->singleton($this->moduleCommands["DeleteDependency"], function ($app) {
            return new DeleteDependencyCommand(
                $app["module.service.manager"],
                $app["module.service.dependency_handler"]
            );
        });
    }

    protected function createDependenciesCommand(): void
    {
        $this->app->singleton($this->moduleCommands["Dependencies"], function ($app) {
            return new DependenciesCommand(
                $app["module.service.manager"],
                $app["module.service.dependency_handler"]
            );
        });
    }

    protected function createInitCommand(): void
    {
        $this->app->singleton($this->moduleCommands["Init"], function ($app) {
            return new InitModuleCommand(
                $app["module.factory.bootstrap"],
                $app["composer"],
                $app["module.service.composer_editor"],
                $app["module.factory.config"],
                $app["files"],
                $app["module.service.manager"],
                $app["module.factory.migration"],
                $app["module.factory.tracker"]
            );
        });
    }

    protected function createNewCommand(): void
    {
        $this->app->singleton($this->moduleCommands["New"], function ($app) {
            return new NewModuleCommand(
                $app["module.factory.module"],
                $app["module.service.manager"]
            );
        });
    }

    protected function createSetCommand(): void
    {
        $this->app->singleton($this->moduleCommands["Set"], function ($app) {
            return new SetWorkbenchCommand(
                $app["module.service.manager"]
            );
        });
    }

    protected function createUnsetCommand(): void
    {
        $this->app->singleton($this->moduleCommands["Unset"], function ($app) {
            return new UnsetWorkbenchCommand(
                $app["module.service.manager"]
            );
        });
    }

    /**
     *******************************************************************************************
     * End of module package commands
     *******************************************************************************************
     */

    public function provides(): array
    {
        return array_values($this->moduleCommands);
    }
}
