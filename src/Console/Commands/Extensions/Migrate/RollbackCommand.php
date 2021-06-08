<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate;

use Illuminate\Database\Console\Migrations\RollbackCommand as OriginalCommand;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\MigrateOverrideTrait;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\ModulesCommandTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class RollbackCommand extends OriginalCommand
{
    use ModulesCommandTrait;
    use MigrateOverrideTrait;

    public function __construct(Migrator $migrator, ModuleManager $moduleManager)
    {
        parent::__construct($migrator);

        $this->moduleManager = $moduleManager;
    }

    /**
     * Get the migration paths to be rolled back
     *
     * @return array|string
     */
    protected function getMigrationPaths()
    {
        $module = $this->getLastMigrationModule();

        if ($module !== null) {
            return $this->getMigrationPathByModule($module);
        }

        return $this->parentCall("getMigrationPaths");
    }

    /**
     * Get the last module that was migrated
     *
     * @return null|string
     */
    protected function getLastMigrationModule ()
    {
        if (Schema::hasColumn("migrations", "module") === false) {
            return null;
        }

        $result = DB::table("migrations")
            ->select(["module", "batch"])
            ->groupBy(["module", "batch"])
            ->orderBy("batch")
            ->get("module")
            ->last()->module;
        ;

        return $result;
    }
}
