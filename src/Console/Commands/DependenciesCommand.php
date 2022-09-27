<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class DependenciesCommand extends ModuleCommand
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

    protected DependencyHandler $handler;

    public function __construct(ModuleManager $manager, DependencyHandler $handler)
    {
        parent::__construct($manager);

        $this->handler = $handler;
    }

    public function handle(): ?bool
    {
        // Get the module from the options
        $module = $this->option("module");

        if (!$this->moduleManager->isInitialised()) {
            $this->displayInitialisationError();
            return false;
        }

        // If the option is not used, use the workbench
        if ($module === false) {
            $module = $this->moduleManager->getWorkbench();
        }

        // If neither are used, return an error message
        if ($module === null) {
            $this->noModuleProvidedResponse();
            return null;
        }

        // And if the module does not exist, I should also get an error message
        if (!$this->moduleManager->hasModule($module)) {
            $this->displayModuleNotFoundError($module);
            return false;
        }

        // Split the modules into groups and display them
        $this->displayUpstreamModules($upstream = $this->handler->getUpstreamModules($module));
        $this->displayCurrentModule($module);
        $this->displayDownstreamModules($downstream = $this->handler->getDownstreamModules($module));
        $unrelatedModules = $this->moduleManager->getActiveModules();
        $this->displayUnrelatedModules(array_diff($unrelatedModules, $upstream, $downstream, [$module]));

        return null;
    }

    protected function noModuleProvidedResponse(): void
    {
        $this->error("No module option was provided, nor was a module found in your workbench.");
    }

    protected function displayUpstreamModules(array $modules): void
    {
        foreach ($modules as $module) {
            $this->info("$module (upstream)");
        }
    }

    protected function displayCurrentModule(string $module): void
    {
        $this->info("$module (current)");
    }

    protected function displayDownstreamModules(array $modules): void
    {
        foreach ($modules as $module) {
            $this->info("$module (downstream)");
        }
    }

    protected function displayUnrelatedModules(array $modules): void
    {
        foreach ($modules as $module) {
            $this->info("$module (unrelated)");
        }
    }
}
