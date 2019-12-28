<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate;

use Illuminate\Database\Console\Migrations\FreshCommand as OriginalCommand;
use Symfony\Component\Console\Input\InputOption;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\MigrateOverrideTrait;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\ModulesCommandTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class FreshCommand extends OriginalCommand
{
    use ModulesCommandTrait;
    use MigrateOverrideTrait;

    public function __construct(ModuleManager $moduleManager)
    {
        parent::__construct();

        $this->moduleManager = $moduleManager;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws ModulesNotInitialisedException
     * @throws ModuleNotFoundException
     */
    public function handle () : void
    {
        // Try to get the workbench module and clear the workbench. If the modules are no initialised, default to default functionality
        try {
            $workbench = $this->moduleManager->getWorkBench();
            $this->moduleManager->clearWorkbench();
        } catch (ModulesNotInitialisedException $e) {
            parent::handle();
            return;
        }

        $database = $this->input->getOption('database');
        // Run the normal fresh command
        parent::handle();

        // Foreach module specified in the modules command, run a migration
        foreach ($this->getModules() as $module) {
            $this->moduleManager->setWorkbench($module);

            $this->call('migrate', array_filter([
                '--database' => $database,
                '--path' => $this->input->getOption('path'),
                '--realpath' => $this->input->getOption('realpath'),
                '--force' => true,
                '--step' => $this->option('step'),
            ]));
        }

        // Set the workbench back to its original value, if any.
        if ($workbench !== null) {
            $this->moduleManager->setWorkbench($workbench);
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ["modules", null, InputOption::VALUE_OPTIONAL, "The modules you want included in this migration."];

        return $options;
    }
}
