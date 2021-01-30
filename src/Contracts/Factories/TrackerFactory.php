<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface TrackerFactory extends FileFactory
{
    /**
     * Create a tracker file in the modules directory
     *
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    public function create(string $rootDir): void;
}
