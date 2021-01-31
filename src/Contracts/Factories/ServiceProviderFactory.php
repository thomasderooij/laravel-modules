<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;

interface ServiceProviderFactory
{
    /**
     * Create a new file based on a stub
     *
     * @param string $module
     * @throws FileNotFoundException
     * @throws ConfigFileNotFoundException
     * @return void
     */
    public function create (string $module) : void;
}
