<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class DependenciesModuleCommand extends ModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:dependencies {--module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'See what modules are related to this module';

    protected $handler;

    public function __construct (ModuleManager $manager, DependencyHandler $handler)
    {
        parent::__construct($manager);

        $this->handler = $handler;
    }

    public function handle()
    {
        // Get the module from the options
        $module = $this->option("module");

        // If the option is not used, use the workbench
        if ($module === null) {
            $module = $this->moduleManager->getWorkbench();
        }

        // If neither are used, return an error message
        if ($module === null) {
            $this->noModuleProvidedResponse();
            return;
        }

        // Split the modules into groups and display them
        $this->displayUpstreamModules($upstream = $this->handler->getUpstreamModules($module));
        $this->displayCurrentModule($module);
        $this->displayDownstreamModules($downstream = $this->handler->getDownstreamModules($module));
        $unrelatedModules = $this->moduleManager->getActiveModules();
        $this->displayUnrelatedModules(array_diff($unrelatedModules, $upstream, $downstream, [$module]));

    }

    protected function noModuleProvidedResponse () : void
    {
        $this->error("No module option was provided, nor was a module found in your workbench.");
    }

    protected function displayUpstreamModules (array $modules) : void
    {

    }

    protected function displayCurrentModule (string $module) : void
    {

    }

    protected function displayDownstreamModules (array $modules) : void
    {

    }

    protected function displayUnrelatedModules (array $modules) : void
    {

    }
}
