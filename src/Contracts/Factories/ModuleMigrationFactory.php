<?php

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface ModuleMigrationFactory
{
    /**
     * Create a new migration file
     * @throws FileNotFoundException
     */
    public function create () : void;

    /**
     * Remove the migration file
     */
    public function undo () : void;
}
