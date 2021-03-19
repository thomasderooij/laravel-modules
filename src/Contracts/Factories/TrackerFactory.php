<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface TrackerFactory extends FileFactory
{
    /**
     * Create a tracker file in the modules directory
     */
    public function create(string $rootDir): void;
}
