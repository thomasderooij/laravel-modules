<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface ConfigFactory
{
    /**
     * Create modules config files and metadata files
     */
    public function create (string $appNamespace, string $rootDir) : void;

    public function undo () : void;
}
