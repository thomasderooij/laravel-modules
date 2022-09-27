<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate;

use Illuminate\Database\Console\Migrations\FreshCommand as OriginalCommand;
use Symfony\Component\Console\Input\InputOption;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\MigrateOverrideTrait;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\ModulesCommandTrait;
use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class FreshCommand extends OriginalCommand
{
    use ModulesCommandTrait;
    use MigrateOverrideTrait;

    public function __construct(ModuleManager $moduleManager, DependencyHandler $dependencyHandler)
    {
        parent::__construct();

        $this->moduleManager = $moduleManager;
        $this->dependencyHandler = $dependencyHandler;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // Try to get the workbench module and clear the workbench. If the modules are no initialised, default to default functionality
        if (!$this->moduleManager->isInitialised()) {
            $this->parentCall("handle");
            return;
        }

        $database = $this->input->getOption('database');
        // Wipe the db
        $this->parentCall("call", [
            "db:wipe",
            array_filter([
                '--database' => $database,
                '--drop-views' => $this->option('drop-views'),
                '--drop-types' => $this->option('drop-types'),
                '--force' => true,
            ])
        ]);

        // And run migrate with the modules command
        $this->parentCall("call", [
            "migrate",
            array_filter([
                '--database' => $database,
                '--path' => $this->input->getOption('path'),
                '--realpath' => $this->input->getOption('realpath'),
                '--force' => true,
                '--step' => $this->option('step'),
                '--modules' => $this->option('modules')
            ])
        ]);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $options = $this->parentCall("getOptions");
        $options[] = ["modules", null, InputOption::VALUE_OPTIONAL, "The modules you want included in this migration."];

        return $options;
    }
}
