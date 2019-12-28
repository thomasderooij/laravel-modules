<?php

namespace Thomasderooij\LaravelModules;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Thomasderooij\LaravelModules\Console\CompositeKernel;
use Thomasderooij\LaravelModules\Factories\AppBootstrapFactory;
use Thomasderooij\LaravelModules\Factories\AuthServiceProviderFactory;
use Thomasderooij\LaravelModules\Factories\BroadcastServiceProviderFactory;
use Thomasderooij\LaravelModules\Factories\ConfigFactory;
use Thomasderooij\LaravelModules\Factories\ConsoleKernelFactory;
use Thomasderooij\LaravelModules\Factories\ControllerFactory;
use Thomasderooij\LaravelModules\Factories\EventServiceProviderFactory;
use Thomasderooij\LaravelModules\Factories\ModuleFactory;
use Thomasderooij\LaravelModules\Factories\ModuleMigrationFactory;
use Thomasderooij\LaravelModules\Factories\RouteFactory;
use Thomasderooij\LaravelModules\Factories\RouteServiceProviderFactory;
use Thomasderooij\LaravelModules\Services\ComposerEditor;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Services\ModuleMigrationRepository;
use Thomasderooij\LaravelModules\Services\ModuleMigrator;
use Thomasderooij\LaravelModules\Services\RouteSource;

class ModuleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    protected $moduleServices = [
        "AuthServiceProviderFactory"        => "module.factory.service_provider.auth",
        "BootstrapFactory"                  => "module.factory.bootstrap",
        "BroadcastServiceProviderFactory"   => "module.factory.service_provider.broadcast",
        "ComposerEditor"                    => "module.service.composer_editor",
        "CompositeKernel"                   => "module.kernel.console_composite_kernel",
        "ConfigFactory"                     => "module.factory.config",
        "ConsoleKernelFactory"              => "module.factory.console_kernel",
        "ControllerFactory"                 => "module.factory.controller",
        "EventServiceProviderFactory"       => "module.factory.service_provider.event",
        "ModuleFactory"                     => "module.factory.module",
        "ModuleManager"                     => "module.service.manager",
        "ModuleMigrator"                    => "migrator",
        "ModuleMigrationFactory"            => "module.factory.migration",
        "ModuleMigrationRepository"         => "migration.repository",
        "RouteFactory"                      => "module.factory.route",
        "RouteServiceProviderFactory"       => "module.factory.service_provider.route",
        "RouteSource"                       => "module.service.route_source",
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() : void
    {
        $this->registerServices();
    }

    /**
     *******************************************************************************************
     * Module services
     *******************************************************************************************
     *
     * These services should be triggered by the loop in the registerServices function
     */
    protected function registerServices () : void
    {
        $this->registerBootstrapFactory();
        $this->registerConfigFactory();
        $this->registerConsoleCompositeKernel();
        $this->registerConsoleKernelFactory();
        $this->registerControllerFactory();
        $this->registerModuleFactory();
        $this->registerModuleMigrationFactory();
        $this->registerRouteFactory();
        $this->registerAuthServiceProviderFactory();
        $this->registerBroadcastServiceProviderFactory();
        $this->registerEventServiceProviderFactory();
        $this->registerRouteServiceProviderFactory();
        $this->registerComposerEditor();
        $this->registerModuleManager();
        $this->registerRouteSource();
        $this->registerModuleMigrator();
        $this->registerModuleMigrationRepository();
    }

    protected function registerBootstrapFactory () : void
    {
        $this->app->singleton($this->moduleServices["BootstrapFactory"], function ($app) {
            return new AppBootstrapFactory(
                $app["files"],
                $app[$this->moduleServices["CompositeKernel"]],
                $app[$this->moduleServices["ModuleManager"]]
            );
        });
    }

    protected function registerConfigFactory () : void
    {
        $this->app->singleton($this->moduleServices["ConfigFactory"], function ($app) {
            return new ConfigFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]],
                $app[$this->moduleServices["ComposerEditor"]]
            );
        });
    }

    protected function registerConsoleCompositeKernel () : void
    {
        $this->app->singleton($this->moduleServices["CompositeKernel"], function ($app) {
            return new CompositeKernel(
                $app,
                $app["events"]
            );
        });
    }

    protected function registerConsoleKernelFactory () : void
    {
        $this->app->singleton($this->moduleServices["ConsoleKernelFactory"], function ($app) {
            return new ConsoleKernelFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]],
                $app[$this->moduleServices["RouteSource"]]
            );
        });
    }

    protected function registerControllerFactory () : void
    {
        $this->app->singleton($this->moduleServices["ControllerFactory"], function ($app) {
            return new ControllerFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]]
            );
        });
    }

    protected function registerModuleFactory () : void
    {
        $this->app->singleton($this->moduleServices["ModuleFactory"], function ($app) {
            return new ModuleFactory(
                $app[$this->moduleServices["RouteFactory"]],
                $app[$this->moduleServices["RouteServiceProviderFactory"]],
                $app[$this->moduleServices["ConsoleKernelFactory"]],
                $app[$this->moduleServices["ControllerFactory"]],
                $app[$this->moduleServices["AuthServiceProviderFactory"]],
                $app[$this->moduleServices["BroadcastServiceProviderFactory"]],
                $app[$this->moduleServices["EventServiceProviderFactory"]],
                $app[$this->moduleServices["ModuleManager"]]
            );
        });
    }

    protected function registerModuleMigrationFactory () : void
    {
        $this->app->singleton($this->moduleServices["ModuleMigrationFactory"], function ($app) {
            return new ModuleMigrationFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]]
            );
        });
    }

    protected function registerRouteFactory () : void
    {
        $this->app->singleton($this->moduleServices["RouteFactory"], function ($app) {
            return new RouteFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]],
                $app[$this->moduleServices["RouteSource"]]
            );
        });
    }

    protected function registerAuthServiceProviderFactory () : void
    {
        $this->app->singleton($this->moduleServices["AuthServiceProviderFactory"], function ($app) {
            return new AuthServiceProviderFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]],
                $app[$this->moduleServices["RouteSource"]]
            );
        });
    }

    protected function registerBroadcastServiceProviderFactory () : void
    {
        $this->app->singleton($this->moduleServices["BroadcastServiceProviderFactory"], function ($app) {
            return new BroadcastServiceProviderFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]],
                $app[$this->moduleServices["RouteSource"]]
            );
        });
    }

    protected function registerEventServiceProviderFactory () : void
    {
        $this->app->singleton($this->moduleServices["EventServiceProviderFactory"], function ($app) {
            return new EventServiceProviderFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]],
                $app[$this->moduleServices["RouteSource"]]
            );
        });
    }

    protected function registerRouteServiceProviderFactory () : void
    {
        $this->app->singleton($this->moduleServices["RouteServiceProviderFactory"], function ($app) {
            return new RouteServiceProviderFactory(
                $app["files"],
                $app[$this->moduleServices["ModuleManager"]],
                $app[$this->moduleServices["RouteSource"]]
            );
        });
    }

    protected function registerComposerEditor () : void
    {
        $this->app->singleton($this->moduleServices["ComposerEditor"], function ($app) {
            return new ComposerEditor(
                $app["files"]
            );
        });
    }

    protected function registerModuleManager () : void
    {
        $this->app->singleton($this->moduleServices["ModuleManager"], function ($app) {
            return new ModuleManager();
        });
    }

    protected function registerRouteSource () : void
    {
        $this->app->singleton($this->moduleServices["RouteSource"], function ($app) {
            return new RouteSource();
        });
    }

    protected function registerModuleMigrator () : void
    {
        $this->app->singleton($this->moduleServices["ModuleMigrator"], function ($app) {
            return new ModuleMigrator(
                $app['migration.repository'],
                $app['db'],
                $app['files'],
                $app['events']
            );
        });
    }

    protected function registerModuleMigrationRepository () : void
    {
        $this->app->singleton($this->moduleServices["ModuleMigrationRepository"], function ($app) {
            return new ModuleMigrationRepository(
                $app['db'],
                $app['config']['database.migrations']
            );
        });
    }

    /**
     *******************************************************************************************
     * End of services
     *******************************************************************************************
     */

    public function provides()
    {
        return array_keys($this->moduleServices);
    }
}
