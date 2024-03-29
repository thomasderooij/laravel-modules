<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleMigrationFactory as Contract;

class ModuleMigrationFactory extends FileFactory implements Contract
{
    /**
     * Create a new migration file
     * @throws FileNotFoundException
     */
    public function create(): void
    {
        $absoluteMigrationDir = base_path($this->getRelativeMigrationDir());

        $this->populateFile($absoluteMigrationDir, $this->getMigrationName(), $this->getStub(), [
            $this->getMigrationClassPlaceholder() => $this->getMigrationClassName()
        ]);
    }

    /**
     * Remove the migration file
     */
    public function undo(): void
    {
        if ($this->filesystem->isFile(base_path($this->getRelativeMigrationDir()) . "/" . $this->getMigrationName())) {
            $this->filesystem->delete(base_path($this->getRelativeMigrationDir()) . "/" . $this->getMigrationName());
        }
    }

    /**
     * Get the migration classname placeholder
     *
     * @return string
     */
    protected function getMigrationClassPlaceholder(): string
    {
        return "{MigrationClassName}";
    }

    /**
     * Get the migration classname
     *
     * @return string
     */
    protected function getMigrationClassName(): string
    {
        return "ModuleInitMigration";
    }

    /**
     * Get the migration classname
     *
     * @return string
     */
    protected function getMigrationName(): string
    {
        return "2010_11_01_000000_module_init_migration.php";
    }

    /**
     * Get the relative directory of the migrations directory
     *
     * @return string
     */
    protected function getRelativeMigrationDir(): string
    {
        return "database/migrations";
    }

    /**
     * Get the migration stub file location
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . "/stubs/moduleMigration.stub";
    }
}
