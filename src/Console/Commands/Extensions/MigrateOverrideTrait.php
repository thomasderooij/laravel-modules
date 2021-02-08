<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions;

use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

trait MigrateOverrideTrait
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * Get the migration paths
     *
     * @param null|string $module
     * @return array
     */
    protected function getMigrationPaths (string $module = null) : array
    {
        // If there is no path specified, and there is a module, return the module migration path
        if (!$this->option("path") && $module !== null) {
            return [$this->getMigrationPathByModule($module)];
        }

        // Return the default migration path
        return parent::getMigrationPaths();
    }

    /**
     * Get the migration path of a specific module
     *
     * @param string $module
     * @return string
     */
    protected function getMigrationPathByModule (string $module) : string
    {
        return base_path(config("modules.root") . "/$module/database/migrations");
    }
}
