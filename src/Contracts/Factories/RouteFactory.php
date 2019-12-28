<?php


namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface RouteFactory
{
    /**
     * Create route files
     *
     * @param string $module
     * @throws FileNotFoundException
     */
    public function create (string $moduleName) : void;
}
