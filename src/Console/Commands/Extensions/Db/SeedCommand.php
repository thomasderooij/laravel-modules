<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Db;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Console\Seeds\SeedCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class SeedCommand extends OriginalCommand
{
    protected ModuleManager $moduleManager;

    public function __construct(Resolver $resolver, ModuleManager $moduleManager)
    {
        parent::__construct($resolver);

        $this->moduleManager = $moduleManager;
    }

    /**
     * Get a seeder instance from the container.
     *
     * @return \Illuminate\Database\Seeder
     */
    protected function getSeeder()
    {
        $class = $this->input->getArgument('class') ?? $this->input->getOption('class');

        if (strpos($class, '\\') === false) {
            $class = 'Database\\Seeders\\'.$class;

            if (!class_exists('Database\\Seeders\\'.$class)) {
                foreach ($this->moduleManager->getActiveModules() as $module) {
                    $modulesNs = $this->moduleManager->getModuleNamespace($module);
                    $seeder = $modulesNs . "Database\\Seeders";
                    if (class_exists($seeder)) {
                        $class = $seeder;
                        break;
                    }
                }
            }
        }

        if ($class === 'Database\\Seeders\\DatabaseSeeder' &&
            ! class_exists($class)) {
            $class = 'DatabaseSeeder';
        }

        return $this->laravel->make($class)
            ->setContainer($this->laravel)
            ->setCommand($this);
    }
}
