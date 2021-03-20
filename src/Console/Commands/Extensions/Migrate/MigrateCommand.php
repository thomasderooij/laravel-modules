<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\MigrateCommand as OriginalCommand;
use Illuminate\Database\Migrations\Migrator;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\MigrateOverrideTrait;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\ModulesCommandTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Services\DependencyHandler;

class MigrateCommand extends OriginalCommand
{
    use ModulesCommandTrait;
    use MigrateOverrideTrait;

    public function __construct(Migrator $migrator, Dispatcher $dispatcher, ModuleManager $moduleManager, DependencyHandler $dependencyHandler)
    {
        // Add a modules option to the command signature
        $this->signature.= "\n                {--modules= : Migrate a only migrations in the scope of a given modules }";
        $this->moduleManager = $moduleManager;
        $this->dependencyHandler = $dependencyHandler;

        parent::__construct($migrator, $dispatcher);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->prepareDatabase();
        $modules = $this->getModules();

        // Next, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        try {
            foreach ($modules as $module) {
                // If the vanilla laravel is called as a module, migrate normally.
                if (strtolower($module) === strtolower(config("modules.vanilla") ?? "")) {
                    parent::handle();
                    continue;
                }

                // Give a notice if a given module is not recognised
                if (!$this->moduleManager->hasModule($module)) {
                    $this->displayModuleNotFoundWarning($module);
                    continue;
                }

                // Run the files that are yet to be migrated
                $this->migrator->setOutput($this->output)
                    ->run($this->getMigrationPaths($module), [
                        'pretend' => $this->option('pretend'),
                        'step' => $this->option('step'),
                        'module' => $module,
                    ]);
            }
        } catch (ModulesNotInitialisedException $e) {
            $this->displayModulesNotInitialisedError($e->getMessage());
            return;
        }

        // Finally, if the "seed" option has been given, we will re-run the database
        // seed task to re-populate the database, which is convenient when adding
        // a migration and a seed at the same time, as it is only this command.
        if ($this->option('seed') && ! $this->option('pretend')) {
            $this->call('db:seed', ['--force' => true]);
        }
    }
}
