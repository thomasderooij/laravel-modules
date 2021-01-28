<?php

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface ConfigFactory
{
    /**
     * Create modules config files and metadata files
     *
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    public function create (string $rootDir) : void;

    public function undo () : void;
}
